<?php

namespace Barwenock\VideoImport\Service;

class ApiVideoImporter
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
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl                $curl,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $videoUrl
     * @param $sku
     * @return void
     * @throws \Exception
     */
    public function updateProductWithExternalVideo($videoUrl, $sku)
    {
        try {
            $baseUrl =  $this->storeManager->getStore()->getBaseUrl();
            $accessToken = $this->scopeConfig->getValue(
                'video_import/general/access_token',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            $productData = $this->getProductData($baseUrl, $sku, $accessToken);

            $videoInfo = $this->getVideoInfoFromYoutube($videoUrl);

            $serviceUrl = $baseUrl . "/rest/V1/products";
            $productData = [
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
            $this->curl->post($serviceUrl, $this->serializer->serialize($productData));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $videoUrl
     * @return array
     * @throws \Exception
     */
    protected function getVideoInfoFromYoutube($videoUrl = null)
    {
        try {
            sleep(10) ; // 10 seconds delay to avoid youtube api quota limit

            //  API key YouTube.
            $apiKey = $this->scopeConfig->getValue(
                'catalog/product_video/youtube_api_key',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            $pattern = '/[?&]v=([a-zA-Z0-9_-]+)/';
            // Use preg_match to find the video ID
            if (preg_match($pattern, $videoUrl, $matches)) {
                $videoId = $matches[1];
            }

            $ch = curl_init("https://www.googleapis.com/youtube/v3/videos?id=$videoId&key=$apiKey&part=snippet");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($result, true);

            if (!isset($data['items'][0])) {
                throw new \Exception('No video information found.');
            }

            $videoInfo = $data['items'][0]['snippet'];

            return [
                'title' => $videoInfo['title'],
                'description' => $videoInfo['description'],
                'thumbnail_url' => $videoInfo['thumbnails']['default']['url'],
                'thumbnail_path' => $this
                    ->getThumbnailPath($data['items'][0]["snippet"]["thumbnails"]["default"]["url"]),
                'meta' => json_encode($videoInfo),
            ];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    protected function getThumbnailPath($url)
    {
        $imageData = file_get_contents($url);

        if ($imageData === false) {
            throw new \Exception('Could not download image from URL: ' . $url);
        }

        // Конвертируем изображение в формат base64
        $base64Image = base64_encode($imageData);

        return $base64Image;
    }

    /**
     * @param $baseUrl
     * @param $sku
     * @param $token
     * @return array|bool|float|int|string|null
     */
    protected function getProductData($baseUrl, $sku, $token)
    {
        $serviceUrl = $baseUrl . "/rest/V1/products/" . $sku;

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $token);
        $this->curl->get($serviceUrl);
        $response = $this->serializer->unserialize($this->curl->getBody());

        return $response;
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
