<?php

namespace Fedot\StorageBundle\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class UploadTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $client = new Client(
            [
                'base_uri' => 'http://127.0.0.1:8000/',
            ]
        );

        $filePath         = __DIR__.'/Resources/image.jpg';
        $fileSha1Checksum = sha1_file($filePath);
        $fileResource     = fopen($filePath, 'r');
        $fileStream       = \GuzzleHttp\Psr7\stream_for($fileResource);

        $uploadResponse = $client->request('POST', '/storage/upload', ['body' => $fileStream]);

        $this->assertEquals(201, $uploadResponse->getStatusCode());
        $body      = $uploadResponse->getBody();
        $bodyArray = json_decode($body, true);
        $this->assertArrayHasKey('fileId', $bodyArray);
        $fileId = $bodyArray['fileId'];


        $downloadResponse = $client->request('GET', '/storage/download', [
            'body' => json_encode(['fileId' => $fileId]),
        ]);

        $downloadFilePath     = '/tmp/downloadTestFile';
        $downloadFileResource = fopen($downloadFilePath, 'w+');
        stream_copy_to_stream($downloadResponse->getBody()->detach(), $downloadFileResource);

        $downloadFileSha1Checksum = sha1_file($downloadFilePath);

        $this->assertEquals($fileSha1Checksum, $downloadFileSha1Checksum);
    }
}
