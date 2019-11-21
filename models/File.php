<?php

namespace sky\yii\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\imagine\Image;
use Imagine\Image\ImageInterface;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $file_name
 * @property string $content_type
 * @property string $extension
 * @property string $size
 * @property int $download
 * @property bool $is_public
 * @property string $bucket
 * @property string $object_name
 * @property string $path
 * @property string $token
 * @property int $resize
 * @property int $created_at
 * @property int $updated_at
 * 
 */
class File extends \sky\yii\db\ActiveRecord
{
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
            [['file_name', 'content_type', 'size', 'path', 'origin_path'], 'required', 'enableClientValidation' => false, 'when' => function ($model) {
                return !$this->file;
            }],
            [['created_at', 'updated_at', 'download'], 'integer'],
            [['resize', 'is_public'], 'boolean'],
            [['file'], 'file'],
            [['file_name', 'content_type', 'extension', 'size', 'path', 'object_name', 'bucket'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file_name' => Yii::t('app', 'File Name'),
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
    
    public function getFullPath($type = null)
    {
        if ($this->resize) {
            $type = $type ? '-' . $type : '';
        } else {
            $type = '';
        }
        return $this->path . '/' . $this->file_name . $type . '.' . $this->extension;
    }
    
    public static function upload(UploadedFile $file, $savePath, $resize = true)
    {
        if (!$file || $file->hasError) {
            return false;
        }
        $compress = [
            'origin' => $file,
        ];
        if ($resize) {
            $compress = ArrayHelper::merge($compress, static::resize($file));
        }
        $result = null;
        foreach ($compress as $type => $fileCompress) {
            $result = Yii::$app->bucket->upload($fileCompress, $fileCompress->baseName, $savePath);
        }
        /* @var $object \Google\Cloud\Storage\StorageObject */
        
        return [
            'extension' => $file->extension,
            'size' => (string) $file->size,
            'file_name' => $file->baseName,
            'content_type' => $file->type,
            'path' => $savePath,
            'origin_path' => $savePath . '/' . $file->name,
            'resize' => (bool) $resize,
            'object_name' => $object->name(),
            'bucket' => ArrayHelper::getValue($result, 'object'),
        ];
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
        $this->token = Yii::$app->security->generateRandomString(32) . '-' . time();
        if ($upload = static::upload($this->file, $this->savePath)) {
            $this->setAttributes($upload);
            return parent::beforeSave($insert);
        }
    }
    
    public function afterDelete() {
        if ($this->resize) {
            foreach ($attributes as $attribute) {
                Yii::$app->s3->delete($this->{$attribute});
            }
        }
        return parent::afterDelete();
    }
    
    public function getObject()
    {
        echo '<pre>';
        return Yii::$app->bucket->getBucket()->object($this->getFullPath());
    }
}
