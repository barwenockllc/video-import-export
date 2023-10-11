<?php

namespace Barwenock\VideoImport\Model;

class VideoImportList
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * List of videoImports
     *
     * @var \Barwenock\VideoImport\Api\VideoImportInterface[]|string[]
     */
    protected $videoImports;

    /**
     * @var bool
     */
    protected $isVideoImportVerified;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Barwenock\VideoImport\Api\VideoImportInterface[]|string[] $notifiers
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $videoImports = [])
    {
        $this->objectManager = $objectManager;
        $this->videoImports = $videoImports;
        $this->isVideoImportVerified = false;
    }

    /**
     * Returning list of video imports.
     *
     * @return \Barwenock\VideoImport\Api\VideoImportInterface[]
     * @throws \InvalidArgumentException
     */
    public function asArray()
    {
        if (!$this->isVideoImportVerified) {
            $hasErrors = false;
            foreach ($this->videoImports as $classIndex => $class) {
                $notifier = $this->objectManager->get($class);
                if ($notifier instanceof \Barwenock\VideoImport\Api\VideoImportInterface) {
                    $this->videoImports[$classIndex] = $notifier;
                } else {
                    $hasErrors = true;
                    unset($this->videoImports[$classIndex]);
                }
            }
            $this->isVideoImportVerified = true;
            if ($hasErrors) {
                throw new \InvalidArgumentException('All notifiers should implement VideoImportInterface');
            }
        }
        return $this->videoImports;
    }
}
