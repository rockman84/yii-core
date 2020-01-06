<?php
namespace sky\yii\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use sky\yii\models\File;
use sky\yii\helpers\Inflector;


/**
 * @property BaseActiveRecord $owner
 * @property string $path
 */
class ActiveUploadBehavior extends \yii\base\Behavior
{
    public $fileAttribute = 'fileUpload';
    
    public $fileIdAttribute = 'file_id';
    
    public $fileModel = 'sky\yii\models\File';
    
    /**
     *
     * @var string | function
     */
    public $savePath = 'upload';
    
    /**
     *
     * @var boolean
     */
    public $resize = true;
    
    
    public function events() {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'upload',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'upload',
        ];
    }
    
    public function upload($event)
    {
        $fileUploaded = $this->owner->{$this->fileAttribute};
        if (!$fileUploaded || !$fileUploaded instanceof UploadedFile) {
            return false;
        }
        $classModel = $this->fileModel;
        $file = $classModel::upload($fileUploaded, $this->getPath(), $this->resize);
        if ($file && $file instanceof File && $file->id) {
            $this->owner->{$this->fileIdAttribute} = $file->id;
            return $file;
        } else {
            $this->owner->addError($this->fileAttribute, Yii::t('app', 'Fail Upload File'));
            return false;
        }
    }

    protected function getPath()
    {
        if (is_string($this->savePath)) {
            return Inflector::replace($this->savePath, $this->owner->getAttributes());
        } elseif (is_callable($this->savePath)) {
            return call_user_func($this->savePath, $this->owner);
        }
    }
}