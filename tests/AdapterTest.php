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

    /**
     * @dataProvider  dropboxProvider
     */
    public function testDelete(Adapter $adapter, $mock) {
        $mock->delete(Argument::type('string'))->willReturn(ModelFactory::make(['name' => "file.pdf"]));
        $response = $adapter->delete('/something');
        $response = $adapter->deleteDir('/something');
        $this->assertTrue($response);
    }

    /**
     * @dataProvider  dropboxProvider
     */
    public function testDeleteFail(Adapter $adapter, $mock) {
        $mock->delete(Argument::any(), Argument::any())->willThrow(new DropboxClientException('Message'));
        $resp = $adapter->delete('something', 'something');
        $resp = $adapter->deleteDir('something', 'something');
        $this->assertFalse($resp);
    }
}