<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Model;

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
     * Get video provider
     *
     * @param string $providerCode
     * @return mixed
     */
    public function getVideoProvider($providerCode)
    {
        if (isset($this->providers[$providerCode])) {
            return $this->providers[$providerCode];
        } else {
            throw new \InvalidArgumentException(sprintf("Provider '%s' not implemented in pool.", $providerCode));
        }
    }
}
