<?php

namespace Tests\Transport;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery;
use ESportsAlliance\TeamSpeakCore\Transport\UDP;
use ESportsAlliance\TeamSpeakCore\Exception\TransportException;

class UDPTest extends TestCase
{
    public function testConstructorNoException()
    {
        $adapter = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->assertInstanceOf(UDP::class, $adapter);

        $this->assertArrayHasKey('host', $adapter->getConfig());
        $this->assertEquals('test', $adapter->getConfig('host'));

        $this->assertArrayHasKey('port', $adapter->getConfig());
        $this->assertEquals(12345, $adapter->getConfig('port'));

        $this->assertArrayHasKey('timeout', $adapter->getConfig());
        $this->assertInternalType(
            PHPUnit_IsType::TYPE_INT,
            $adapter->getConfig('timeout')
        );

        $this->assertArrayHasKey('blocking', $adapter->getConfig());
        $this->assertInternalType(
            PHPUnit_IsType::TYPE_INT,
            $adapter->getConfig('blocking')
        );
    }

    public function testConstructorExceptionNoHost()
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage("config must have a key for 'host'");

        $adapter = new UDP(['port' => 12345]);
    }

    public function testConstructorExceptionNoPort()
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage("config must have a key for 'port'");

        $adapter = new UDP(['host' => 'test']);
    }

    public function testGetConfig()
    {
        $adapter = new UDP(
            ['host' => 'test', 'port' => 12345]
        );

        $this->assertInternalType(
            PHPUnit_IsType::TYPE_ARRAY,
            $adapter->getConfig()
        );
        $this->assertCount(4, $adapter->getConfig());
        $this->assertArrayHasKey('host', $adapter->getConfig());
        $this->assertEquals('test', $adapter->getConfig()['host']);
        $this->assertEquals('test', $adapter->getConfig('host'));
    }

    public function testSetGetAdapter()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        // Mocking adaptor since `stream_socket_client()` depends on running server
        $adaptor = $this->createMock(ServerQuery::class);
        $transport->setAdapter($adaptor);

        $this->assertSame($adaptor, $transport->getAdapter());
    }

    public function testGetStream()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->assertNull($transport->getStream());
    }

    public function testConnect()
    {
        $transport = new UDP(
            ['host' => '127.0.0.1', 'port' => 12345]
        );
        $this->assertNull($transport->connect());
        $this->assertInternalType(
            PHPUnit_IsType::TYPE_RESOURCE,
            $transport->getStream()
        );
    }

    public function testConnectBadHost()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('getaddrinfo failed');
        $this->assertNull($transport->connect());
    }

    public function testDisconnect()
    {
        $transport = new UDP(
            ['host' => '127.0.0.1', 'port' => 12345]
        );
        $transport->connect();
        $this->assertInternalType(
            PHPUnit_IsType::TYPE_RESOURCE,
            $transport->getStream()
        );
    }

    public function testDisconnectNoConnection()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->assertNull($transport->disconnect());
    }

    public function testReadNoConnection()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('getaddrinfo failed');
        $transport->read();
    }

    public function testSendNoConnection()
    {
        $transport = new UDP(
            ['host' => 'test', 'port' => 12345]
        );
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('getaddrinfo failed');
        $transport->send('test.send');
    }
}
