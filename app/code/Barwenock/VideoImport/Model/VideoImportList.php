<?php

namespace Barwenock\VideoImport\Model;

class VideoImportList
{
    /**
     * @var VideoImportPool
     */
    protected $objectPool;

    /**
     * @param VideoImportPool $objectPool
     */
    public function __construct(\Barwenock\VideoImport\Model\VideoImportPool $objectPool)
    {
        $this->objectPool = $objectPool;
    }
}
