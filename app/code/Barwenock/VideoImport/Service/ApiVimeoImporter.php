<?php
declare(strict_types=1);

namespace Barwenock\VideoImport\Service;

class ApiVimeoImporter
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected \Magento\Framework\HTTP\Client\Curl $curl;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->curl = $curl;
    }

    /**
     * @param $videoUrl
     * @return array
     * @throws \Exception
     */
    public function getVideoInfo($videoUrl = null)
    {
        try {
            $pattern = '/vimeo\.com\/(\d+)/';
            // Use preg_match to find the video ID
            if (preg_match($pattern, $videoUrl, $matches)) {
                $videoId = $matches[1];
            }

            $ch = curl_init("https://vimeo.com/api/oembed.json?format=json&url=https://vimeo.com/$videoId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $videoInfo = json_decode($result, true);

            if (!isset($videoInfo['type'])) {
                throw new \Exception('No video information found.');
            }

            return [
                'title' => $videoInfo['title'],
                'description' => $videoInfo['description'],
                'thumbnail_url' => $videoInfo['thumbnail_url'],
                'thumbnail_path' => $this
                    ->getThumbnailPath($videoInfo['thumbnail_url']),
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
