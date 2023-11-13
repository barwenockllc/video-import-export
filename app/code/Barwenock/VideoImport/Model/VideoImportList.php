<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImport\Model;

class VideoImportList
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param $providerCode
     * @return mixed
     */
    public function getVideoProvider($providerCode)
    {
        if (isset($this->providers[$providerCode])) {
            return $this->providers[$providerCode];
        } else {
            throw new \InvalidArgumentException("Provider '$providerCode' not implemented in pool.");
        }
    }
}
