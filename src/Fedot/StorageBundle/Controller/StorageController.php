<?php

namespace Fedot\StorageBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StorageController extends FOSRestController
{
    /**
     * @Route("/upload")
     *
     * @param Request $request
     * @return Response
     */
    public function postFilesAction(Request $request)
    {
        $bodyResource = $request->getContent(true);

        $filesystem = $this->container->get('oneup_flysystem.my_filesystem_filesystem');
        $fileName = uniqid('', true);
        $filesystem->putStream($fileName, $bodyResource);

        return new JsonResponse([
            'fileId' => $fileName,
        ], 201);
    }

    /**
     * @Route("/download")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getFilesAction(Request $request)
    {
        $jsonBody = json_decode($request->getContent(), true);
        $fileName = $jsonBody['fileId'];

        $filesystem = $this->container->get('oneup_flysystem.my_filesystem_filesystem');

        if ($filesystem->has($fileName)) {
            $file = $filesystem->get($fileName);
            $response = new Response();
            $response->headers->set('Content-Length', $file->getSize());
            $response->sendHeaders();

            $output = fopen('php://output', 'w');
            stream_copy_to_stream($file->readStream(), $output);

            return $response;
        }

        return new JsonResponse(['error' => 'file not found'], 404);
    }
}
