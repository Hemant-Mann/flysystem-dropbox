<?php

namespace HemantMann\Flysystem\Dropbox;

use League\Flysystem\Config;
use Kunnu\Dropbox\Dropbox as Client;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

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
        
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath) {
        
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
        
    }

    /**
     * {@inheritdoc}
     */
    public function has($path) {
         
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getClient() {
        
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
}
