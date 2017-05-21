<?php
use Prophecy\Argument;
use League\Flysystem\Config;
use PHPUnit\Framework\TestCase;

use Kunnu\Dropbox\Models\ModelFactory;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use HemantMann\Flysystem\Dropbox\Adapter;

class AdapterTest extends TestCase {
	
    public function setUp() {
        $this->mock = $this->prophesize('Kunnu\Dropbox\Dropbox');
        $this->adapter = new Adapter($this->mock->reveal());
    }

    public function dropboxProvider() {
        $mock = $this->prophesize('Kunnu\Dropbox\Dropbox');

        return [
            [new Adapter($mock->reveal()), $mock]
        ];
    }

    public function metadataProvider() {
        return [
            ['getMetadata'],
            ['getMimetype'],
            ['getTimestamp'],
            ['getSize'],
            ['has'],
        ];
    }

    protected function getFileResponse() {
        return ['.tag' => 'file', 'name' => 'file.pdf', 'path_display' => '/File.pdf', 'id' => 'id:123', 'size' => 21388];
    }

    protected function getFolderResponse() {
        return ['.tag' => 'folder', 'name' => 'foldername', 'path_display' => 'FolderName', 'id' => 'id:123'];
    }

    /**
     * @dataProvider metadataProvider
     */
    public function testGetMetadata($method) {
        $arr = $this->getFileResponse();
        $this->mock->getMetadata(Argument::type('string'), Argument::type('array'))->willReturn(ModelFactory::make($arr));
        $this->assertInternalType('array', $this->adapter->{$method}('one'));
    }

    /**
     * @dataProvider metadataProvider
     */
    public function testMetaDataFail($method) {
        $this->mock->getMetadata(Argument::any(), Argument::any())->willThrow(new DropboxClientException('Message'));
        $resp = $this->adapter->{$method}('one');
        $this->assertFalse($resp);
    }

    public function testDelete() {
        $this->mock->delete(Argument::type('string'))->willReturn(ModelFactory::make(['name' => "file.pdf"]));
        $response = $this->adapter->delete('/something');
        $response = $this->adapter->deleteDir('/something');
        $this->assertTrue($response);

        // let delete fail
        $this->mock->delete(Argument::any(), Argument::any())->willThrow(new DropboxClientException('Message'));
        $resp = $this->adapter->delete('something', 'something');
        $resp = $this->adapter->deleteDir('something', 'something');
        $this->assertFalse($resp);
    }

    public function testCreateDir() {
        $arr = $this->getFolderResponse();
        $this->mock->createFolder(Argument::type('string'))->willReturn(ModelFactory::make($arr));
        $resp = $this->adapter->createDir('/my/cool/dir', new Config());
        
        $this->assertInternalType('array', $resp);
        $this->assertArrayHasKey('type', $resp);
        $this->assertEquals('dir', $resp['type']);

        // Let the create dir fail
        $this->mock->createFolder(Argument::type('string'))->willThrow(new DropboxClientException('Invalid Path'));
        $resp = $this->adapter->createDir('/', new Config());
        $this->assertFalse($resp);
    }

    public function testRename() {
        $this->mock->move(Argument::type('string'), Argument::type('string'))->willReturn(Argument::type('object'));
        $this->assertTrue($this->adapter->rename('something', 'something'));

        $this->mock->move(Argument::type('string'), Argument::type('string'))->willThrow(new DropboxClientException('Message'));
        $this->assertFalse($this->adapter->rename('/something', '/'));
    }

    public function testCopy() {
        $this->mock->copy(Argument::type('string'), Argument::type('string'))->willReturn(Argument::type('object'));
        $this->assertTrue($this->adapter->copy('something', 'something'));

        $this->mock->copy(Argument::type('string'), Argument::type('string'))->willThrow(new DropboxClientException('Message'));
        $this->assertFalse($this->adapter->copy('/something', '/'));
    }

    public function testListContents() {
        $this->mock->listFolder(Argument::any())->willReturn(
            ModelFactory::make( ['entries' => [$this->getFolderResponse()]] ),
            ModelFactory::make( ['entries' => [$this->getFileResponse()]] )
        );
        $result = $this->adapter->listContents('/', true);
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        // No items in the listing because client throws exception
        $this->mock->listFolder(Argument::any())->willThrow(new DropboxClientException('Message'));
        $resp = $this->adapter->listContents('/', false);
        $this->assertCount(0, $resp);
    }

    public function testWrite() {
        $config = new Config(); $config->set('autoRename', false);
        $arr = $this->getFileResponse();

        $this->mock->simpleUpload(Argument::type('string'), Argument::type('string'), Argument::type('array'))
            ->willReturn(ModelFactory::make($arr));

        $result = $this->adapter->write('something', 'contents', $config);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);

        // let the write fail
        $this->mock->simpleUpload(Argument::type('string'), Argument::type('string'), Argument::type('array'))
            ->willThrow(new DropboxClientException('Message'));
        $resp = $this->adapter->write('something', 'contents', $config);
        $this->assertFalse($resp);
    }

    public function testWriteStream() {
        $config = new Config();
        $arr = $this->getFileResponse();
        $this->mock->uploadChunked(Argument::type('string'), Argument::type('string'), Argument::type('integer'), Argument::type('integer'), Argument::type('array'))
            ->willReturn(ModelFactory::make($arr));

        $result = $this->adapter->writeStream('something', tmpfile(), $config);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);

        // let the write fail
        $this->mock->uploadChunked(Argument::type('string'), Argument::type('string'), Argument::type('integer'), Argument::type('integer'), Argument::type('array'))
            ->willThrow(new DropboxClientException('Message'));
        $resp = $this->adapter->writeStream('something', tmpfile(), $config);
        $this->assertFalse($resp);
    }
}