<?php

namespace HemantMann\Flysystem\Dropbox;

use League\Flysystem\Config;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

class Adapter extends AbstractAdapter {
    use NotSupportingVisibilityTrait;

    /**
     * @var Client
     */
    protected $client;

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
        
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($path) {
        
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
        
    }
}
