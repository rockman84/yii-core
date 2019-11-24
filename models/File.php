<?php

namespace sky\yii\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\imagine\Image;
use Imagine\Image\ImageInterface;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use sky\yii\storage\StorageGoogle;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property string $content_type
 * @property string $extension
 * @property string $size
 * @property string $bucket
 * @property string $object_name
 * @property string $path
 * @property string $key
 * @property int $created_at
 * @property int $updated_at
 * 
 */
class File extends \sky\yii\db\ActiveRecord
{
    /**
     *
     * @var UploadedFile
     */
    public $file;
    
    public $savePath = 'file';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }
    
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file', 'savePath'], 'required'],
            [['key'], 'default', 'value' => function () { return Yii::$app->security->generateRandomString(); }],
            [['name'], 'default', 'value' => function () { return $this->file ? $this->file->baseName : null; }],
            [['created_at', 'updated_at'], 'integer'],
            [['file'], 'file'],
            [['name', 'content_type', 'extension', 'path', 'object_name', 'bucket'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'File Name'),
            'content_type' => Yii::t('app', 'Content Type'),
            'extension' => Yii::t('app', 'Extension'),
            'size' => Yii::t('app', 'Size'),
            'path' => Yii::t('app', 'Path'),
            'resize' => Yii::t('app', 'Resize'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    public function getLinkFile($type = null)
    {
        return Yii::$app->bucket->getUrl($this->getFullPath($type));
    }
    
    public static function resizeType()
    {
        return [
            'sm' => [200, 200],
            'md' => [768, 768],
            'lg' => [1000, 1000],
        ];
    }
    
    /**
     * 
     * @param UploadedFile $file
     * @return UploadedFile[]
     */
    public static function resize(UploadedFile $file)
    {
        $result = [];
        $unique = Yii::$app->security->generateRandomString(8);
        foreach (static::resizeType() as $type => $size) {
            $path = Yii::getAlias("@app/runtime/img/{$unique}/");
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }
            $fileName = "{$file->baseName}-{$type}.{$file->extension}";
            $resizePath = $path . $fileName; 
            $image = Image::resize($file->tempName, $size[0], $size[1])->save($resizePath);
            UploadedFile::getInstanceByName($resizePath);
            $result[$type] = new UploadedFile([
                'name' => $fileName,
                'tempName' => $resizePath,
                'type' => $file->type,
            ]);
            
        }
        return $result;
    }
    
    public function beforeSave($insert) {
        /* @var $storage \sky\yii\storage\StorageGoogle */
        $storage = Yii::$app->storage;
        /* @var $fileObject \sky\yii\storage\UploadedStorage */
        $fileObject = $storage->upload($this->file, $this->name, $this->savePath, $this->bucket);
        if ($fileObject) {
            $this->attribute = [
                'size' => $this->file->size,
                'extension' => $this->file->extension,
                'content_type' => $this->file->type,
                'path' => $fileObject->path,
                
            ];
            if ($fileObject->object instanceof \sky\yii\storage\StorageGoogle) {
                $this->attributes = [
                    'bucket' => $fileObject->object->info()['bucket'],
                    'object_name' => $fileObject->object->name(),
                ];
            }
            return parent::beforeSave($insert);
        }
    }
    
    public function afterDelete() {
        return parent::afterDelete();
    }
    
    public function getObject($options = [])
    {
        return $this->getBucket()->getObject($this->object_name, $options);
    }
    
    public function getBucket()
    {
        return Yii::$app->storage->getBucket($this->bucket);
    }
}
