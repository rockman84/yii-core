<?php
namespace sky\yii\behaviors;

use yii\db\BaseActiveRecord;
use sky\yii\models\File;
use yii\web\UploadedFile;

/**
 * @property BaseActiveRecord $owner
 */
class ActiveUploadBehavior extends \yii\base\Behavior
{
    public $fileAttribute = 'file';
    
    public $fileIdAttribute = 'picture_url_id';
    
    /**
     *
     * @var string | function
     */
    public $savePath = '';
    
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
    
    public function upload()
    {
        $fileAttribute = $this->owner->{$this->fileAttribute};
        if (!$fileAttribute || !$fileAttribute instanceof UploadedFile) {
            return false;
        }
        $path = is_callable($this->savePath) ? call_user_func($this->savePath, $this->owner) : $this->savePath;
        $file = File::upload($fileAttribute, $this->savePath, $this->resize);
        if ($file && $file instanceof File) {
            $this->owner->{$this->fileIdAttribute} = $file->id;
            return $file;
        } else {
            $this->owner->addError($this->fileAttribute, Yii::t('app', 'Fail Upload File'));
            return false;
        }
    }    
}