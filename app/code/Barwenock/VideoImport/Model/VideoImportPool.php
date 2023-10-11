<?php

namespace Barwenock\VideoImport\Model;

class VideoImportPool implements \Barwenock\VideoImport\Api\VideoImportInterface
{
    /**
     * @var \Barwenock\VideoImport\Model\VideoImportList
     */
    protected $videoImportList;

    /**
     * @param \Barwenock\VideoImport\Model\VideoImportList $videoImportList
     */
    public function __construct(\Barwenock\VideoImport\Model\VideoImportList $videoImportList)
    {
        $this->videoImportList = $videoImportList;
    }

    public function addVideoImport(/*$severity, $title, $description, $url = '', $isInternal = true*/)
    {
        foreach ($this->videoImportList->asArray() as $videoImport) {
//            $videoImport->addVideoImport($severity, $title, $description, $url, $isInternal);
        }
        return $this;
    }
}
