<?php
namespace sky\yii\storage;

use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use sky\yii\storage\UploadedStorage;
use Yii;

class StorageLocal extends \yii\base\Component
{
    public $bucketName = "@app/web/upload";
    
    public $baseUrl = "@web/upload";

    /**
     * 
     * @param UploadedFile $file
     * @param type $fileName
     * @param type $savePath
     * @return boolean
     */
    public function upload($file, $fileName, $savePath = '', $bucket = null) {
        if (!$file instanceof UploadedFile) {
            return null;
        }
        FileHelper::createDirectory(Yii::getAlias($this->bucketName . '/' . $savePath));
        $filePath = $this->getSavePath($file, $savePath, $fileName);
        if ($file->saveAs($this->getCompleteSavePath($filePath))) {
            return new UploadedStorage([
                'name' => $fileName,
                'path' => $filePath,
                'file' => $file,
                'object' => null,
            ]);
        }
        return false;
    }
    
    public function getUrl($filePath)
    {
        return Yii::getAlias($this->baseUrl . '/' . $filePath);
    }
    
    public function delete($filePath)
    {
        $path = $this->getCompleteSavePath($filePath);
        if (file_exists($path)) {
            return unlink($path);
        }
        return null;
    }
    
    public function getSavePath(UploadedFile $file, $savePath, $fileName)
    {
        $fileName = $fileName ? : $file->baseName;
        return $savePath . '/' . $fileName . '.' . $file->extension;
    }
    
    public function getCompleteSavePath($filePath)
    {
        return Yii::getAlias($this->bucketName . '/' . $filePath);
    }
}
