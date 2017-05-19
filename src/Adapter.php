<?php

namespace HemantMann\Flysystem\Dropbox;

use League\Flysystem\Config;
use League\Flysystem\Util\MimeType;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

use Kunnu\Dropbox\Dropbox as Client;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

class Adapter extends AbstractAdapter {
    use NotSupportingVisibilityTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param string $prefix
     */
    public function __construct(Client $client, $prefix = null) {
        $this->client = $client;
        $this->setPathPrefix($prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config) {

    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function read($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath) {
        $path = $this->applyPathPrefix($path);
        $newpath = $this->applyPathPrefix($newpath);

        try {
            $this->client->move($path, $newpath);
        } catch (DropboxClientException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath) {
        $path = $this->applyPathPrefix($path);
        $newpath = $this->applyPathPrefix($newpath);

        try {
            $this->client->copy($path, $newpath);
        } catch (DropboxClientException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path) {
        $location = $this->applyPathPrefix($path);
        try {
            $obj = $this->client->delete($location);
            if (is_a($obj, 'Kunnu\Dropbox\Models\DeletedMetadata')) {
                return true;
            }
        } catch (DropboxClientException $e) {
            // may be path is wrong
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($path) {
        return $this->delete($path);
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($path, Config $config) {
        $location = $this->applyPathPrefix($path);
        try {
            $result = $this->client->createFolder($location);
            return $this->normalizeResponse($result);
        } catch (DropboxClientException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path) {
        $location = $this->applyPathPrefix($path);
        try {
            $file = $this->client->getMetadata($location, ["include_media_info" => true]);
            return $this->normalizeResponse($file);
        } catch (DropboxClientException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($path) {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path) {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path) {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path) {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false) {
        
    }

    /**
     * Apply the path prefix.
     *
     * @param string $path
     *
     * @return string prefixed path
     */
    public function applyPathPrefix($path) {
        $path = parent::applyPathPrefix($path);
        return '/' . ltrim(rtrim($path, '/'), '/');
    }

    /**
     * Normalize a Dropbox File Response.
     *
     * @param object $obj
     *
     * @return array
     */
    protected function normalizeResponse($obj) {
        $result = ['path' => ltrim($this->removePathPrefix($obj->getPathDisplay()), '/')];

        $objClass = get_class($obj);
        switch ($objClass) {
            case 'Kunnu\Dropbox\Models\FolderMetadata':
                $result['type'] = 'dir';
                break;
            
            case 'Kunnu\Dropbox\Models\FileMetadata':
            default:
                $result['type'] = 'file';
                $result['size'] = $obj->getSize();
                $result['mimetype'] = MimeType::detectByFilename($obj->getName());
                $result['timestamp'] = strtotime($obj->getServerModified());
                break;
        }
        return $result;
    }
}
