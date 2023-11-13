<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImport\Service;

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
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl                $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $videoUrl
     * @return array
     * @throws \Exception
     */
    public function getVideoInfo($videoUrl = null)
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
}
