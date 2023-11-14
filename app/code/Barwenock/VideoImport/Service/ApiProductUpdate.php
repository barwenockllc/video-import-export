<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImport\Service;

class ApiProductUpdate
{

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected \Magento\Framework\HTTP\Client\Curl $curl;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Barwenock\VideoImport\Model\VideoImportList
     */
    protected \Barwenock\VideoImport\Model\VideoImportList $videoImportList;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Barwenock\VideoImport\Model\VideoImportList $videoImportList
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl                $curl,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Barwenock\VideoImport\Model\VideoImportList $videoImportList,
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->videoImportList = $videoImportList;
    }

    /**
     * @param $videoUrl
     * @param $sku
     * @return true
     * @throws \Exception
     */
    public function updateProductWithExternalVideo($videoUrl, $sku)
    {
        try {
            $baseUrl =  $this->storeManager->getStore()->getBaseUrl();
            $accessToken = $this->scopeConfig->getValue(
                'videoimport/general/access_token',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            $productData = $this->getProductData($baseUrl, $sku, $accessToken);

            if (str_contains(trim($videoUrl), 'youtube.com')
                && preg_match('#[?&]v=([a-zA-Z0-9_-]+)#', trim($videoUrl), $matches)) {
                $videoInfo = $this->videoImportList->getVideoProvider('youtube')
                    ->getVideoInfo(trim($videoUrl));
            } elseif (str_contains(trim($videoUrl), 'vimeo.com')
                && preg_match('#vimeo\.com\/(\d+)#', trim($videoUrl), $matches)) {
                $videoInfo = $this->videoImportList->getVideoProvider('vimeo')
                    ->getVideoInfo(trim($videoUrl));
            } else {
                throw new \Exception('No video import service found');
            }

            $serviceUrl = $baseUrl . "rest/V1/products";
            $productUpdateData = [
                "product" => [
                    "sku" => $sku,
                    "media_gallery_entries" => $this->prepareMediaGalleryEntries(
                        $productData['media_gallery_entries'],
                        $videoInfo,
                        $videoUrl
                    )
                ]
            ];

            $this->curl->addHeader("Content-Type", "application/json");
            $this->curl->addHeader("Authorization", "Bearer " . $accessToken);
            $this->curl->post($serviceUrl, $this->serializer->serialize($productUpdateData));

            return true;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param $baseUrl
     * @param $sku
     * @param $token
     * @return array|bool|float|int|string|null
     */
    protected function getProductData($baseUrl, $sku, $token)
    {
        $serviceUrl = $baseUrl . "rest/V1/products/" . $sku;

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $token);
        $this->curl->get($serviceUrl);

        return $this->serializer->unserialize($this->curl->getBody());
    }

    /**
     * @param $productEntries
     * @param $videoInfo
     * @param $videoUrl
     * @return mixed
     */
    protected function prepareMediaGalleryEntries($productEntries, $videoInfo, $videoUrl)
    {
        $video = [
            "media_type" => "external-video",
            "disabled" => false,
            "label" => $videoInfo['title'],
            "types" => [],
            "position" => 1,
            "content" => [
                "type" => "image/jpeg",
                "name" => 'thumbnail.jpeg',
                "base64_encoded_data" => base64_encode(file_get_contents($videoInfo['thumbnail_url']))
            ],
            "extension_attributes" => [
                "video_content" => [
                    "media_type" => "external-video",
                    "video_provider" => "youtube",
                    "video_url" => $videoUrl,
                    "video_title" => $videoInfo['title'],
                    "video_description" => $videoInfo['description'],
                    "video_metadata" => $videoInfo['meta']
                ]
            ]
        ];

        $productEntries[] = $video;

        // Return the updated $productEntries array
        return $productEntries;
    }
}
