<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Service;

class ApiYoutubeImporter
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected \Magento\Framework\HTTP\Client\Curl $curl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected \Magento\Framework\Filesystem\Driver\File $fileDriver;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl                $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Get video info of YouTube service
     *
     * @param string $videoUrl
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVideoInfo($videoUrl = null)
    {
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

        // Resetting header authorization from the previous request
        $this->curl->addHeader('Authorization', null);

        $this->curl->get(sprintf(
            'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet',
            $videoId,
            $apiKey
        ));

        $result = $this->curl->getBody();

        $data = json_decode($result, true);

        if (!isset($data['items'][0])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No video information found.'));
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
    }

    /**
     * Retrieves the base64-encoded thumbnail image data from a given URL
     *
     * @param string $url
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getThumbnailPath($url)
    {
        $imageData = $this->fileDriver->fileGetContents($url);

        return base64_encode($imageData);
    }
}
