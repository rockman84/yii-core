<?php
namespace sky\yii\storage;

/**
 * @property string $filename
 */
class UploadedStorage extends \yii\base\BaseObject
{
    /**
     *
     * @var string
     */
    public $name;
    
    /**
     *
     * @var \Google\Cloud\Storage\StorageObject
     */
    public $object;
    
    /**
     *
     * @var \yii\web\UploadedFile
     */
    public $file;
    
    /**
     *
     * @var string
     */
    public $path;
    
    public function getFileName()
    {
        return $this->name . '.' . $this->file->extension;
    }
}