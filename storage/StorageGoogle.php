<?php
namespace sky\yii\storage;

/**
 * http://googleapis.github.io/google-cloud-php/#/docs/cloud-storage/v1.9.1/storage/readme
 */
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Bucket;
use Yii;
use sky\yii\storage\UploadedStorage;

/**
 * @property StorageClient $storage
 * @property Bucket $bucket
 */
class StorageGoogle extends \sky\yii\storage\StorageLocal
{
    public $fileJsonKey = '@app/config/CASAINDO-839e7155ba44.json';
    
    public $bucketName = 'bucket';
    
    public $preFolder = 'dev';
    
    public $host = 'https://storage.googleapis.com/';
    
    private $_storages;
    
    private $_buckets = [];
    
    public function init() {
        return parent::init();
    }
    
    public function getStorage()
    {
        if (!$this->_storages) {
            $filePath = Yii::getAlias($this->fileJsonKey);
            $this->_storages = new StorageClient([
                'keyFile' => json_decode(file_get_contents($filePath), true)
            ]);
        }
        return $this->_storages;
    }
    
    public function getBucket($bucket = null)
    {
        $bucket = $bucket ? : $this->bucketName;
        if (!isset($this->_buckets[$bucket])) {
            $this->_buckets[$bucket] = $this->storage->bucket($bucket);
        }
        return $this->_buckets[$bucket];
    }
    
    /**
     * 
     * @param \yii\web\UploadedFile $file
     * @param type $fileName
     * @param type $savePath
     * @return UploadedStorage
     */
    public function upload($file, $fileBaseName, $savePath = '', $bucket = null) {
        if (!$file instanceof \yii\web\UploadedFile) {
            return false;
        }
        $fileObject = fopen($file->tempName, 'r');
        $filePath = $this->getSavePath($file, $savePath, $fileBaseName);
        if ($object = $this->getBucket($bucket)->upload($fileObject, ['name' => $this->preFolder . '/' . $filePath ])) {
            unlink($file->tempName);
            return new UploadedStorage([
                'file' => $file,
                'object' => $object,
                'name' => $$fileBaseName,
                'path' => $filePath,
            ]);
        };
        return false;
        
    }
    
    public function getUrl($filePath) {
        return $this->host . $this->bucketName . '/' . $this->preFolder . '/' . $filePath;
    }
}