<?php

namespace Barwenock\VideoImport\Model;

class VideoImportPool
{
    /**
     * @var array
     */
    protected $pool = [];

    /**
     * @var array
     */
    protected $constructorArgs = [];

    /**
     * @param ...$constructorArgs
     * @return VideoProcessor|mixed|null
     */
    public function getObject(...$constructorArgs)
    {
        if (empty($this->pool)) {
            return $this->createObject(...$constructorArgs);
        }
        $object = array_pop($this->pool);
        $this->constructorArgs[$object] = $constructorArgs;
        return $object;
    }

    /**
     * @param $object
     * @return void
     */
    public function releaseObject($object)
    {
        $this->pool[] = $object;
        unset($this->constructorArgs[$object]);
    }

    /**
     * @param ...$constructorArgs
     * @return VideoProcessor
     */
    protected function createObject(...$constructorArgs)
    {
        // Create a new object with constructor arguments here
        return new \Barwenock\VideoImport\Model\VideoProcessor(...$constructorArgs);
    }
}
