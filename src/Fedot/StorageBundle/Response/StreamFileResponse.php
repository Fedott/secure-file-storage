<?php

namespace Fedot\StorageBundle\Response;

use League\Flysystem\File;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StreamFileResponse extends Response
{
    /**
     * @var File
     */
    protected $file;

    /**
     * StreamFileResponse constructor.
     * @param File $file
     * @param int $status
     * @param array $headers
     */
    public function __construct($file, $status = 200, $headers = array())
    {
        if (!$file instanceof File) {
            throw new RuntimeException("Content must be instance of " . File::class);
        }

        parent::__construct(null, $status, $headers);

        $this->setFile($file);
    }

    /**
     * @param File $file
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function prepare(Request $request)
    {
        $this->headers->set('Content-Length', $this->file->getSize());

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', $this->file->getMimeType() ?: 'application/octet-stream');
        }

        if ('HTTP/1.0' != $request->server->get('SERVER_PROTOCOL')) {
            $this->setProtocolVersion('1.1');
        }

        return $this;
    }

    /**
     * Sends the file.
     */
    public function sendContent()
    {
        if (!$this->isSuccessful()) {
            parent::sendContent();

            return;
        }

        $outStream = fopen('php://output', 'wb');
        $fileStream = $this->file->readStream();

        stream_copy_to_stream($fileStream, $outStream);

        fclose($outStream);
        fclose($fileStream);
    }



    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     */
    public function setContent($content)
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a BinaryFileResponse instance.');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return false
     */
    public function getContent()
    {
        return false;
    }
}
