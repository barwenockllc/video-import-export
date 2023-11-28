<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Service;

class ApiVimeoImporter
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->curl = $curl;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Get video info of Vimeo service
     *
     * @param string $videoUrl
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVideoInfo($videoUrl = null)
    {
        $pattern = '/vimeo\.com\/(\d+)/';
        // Use preg_match to find the video ID
        if (preg_match($pattern, $videoUrl, $matches)) {
            $videoId = $matches[1];
        }

        // Resetting header authorization from the previous request
        $this->curl->addHeader('Authorization', null);

        $this->curl->get(sprintf(
            'https://vimeo.com/api/oembed.json?format=json&url=https://vimeo.com/%s',
            $videoId
        ));

        $result = $this->curl->getBody();

        $videoInfo = json_decode($result, true);

        if (!isset($videoInfo['type'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No video information found.'));
        }

        return [
            'title' => $videoInfo['title'],
            'description' => $videoInfo['description'],
            'thumbnail_url' => $videoInfo['thumbnail_url'],
            'thumbnail_path' => $this
                ->getThumbnailPath($videoInfo['thumbnail_url']),
            'meta' => json_encode($videoInfo),
        ];
    }

    /**
     * Retrieves the base64-encoded thumbnail image data from a given URL
     *
     * @param string $url
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getThumbnailPath($url)
    {
        $imageData = $this->fileDriver->fileGetContents($url);

        return base64_encode($imageData);
    }
}
