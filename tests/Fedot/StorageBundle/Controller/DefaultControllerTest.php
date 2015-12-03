<?php

namespace Fedot\StorageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }

    public function testUpload()
    {
        $client = static::createClient();

        $uploadedFile = new UploadedFile(__DIR__ . '/Resources/file', 'file');
        $crawler = $client->request('POST', "/upload", [], [$uploadedFile]);

        $expected = "OK";

        $this->assertEquals($expected, $client->getResponse()->getContent());
    }
}
