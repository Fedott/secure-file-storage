<?php

namespace Fedot\StorageBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
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

        file_put_contents('/tmp/testFile', $bodyResource);

        return new JsonResponse([
            'fileId' => 'qweqwe',
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

        if ("qweqwe" == $jsonBody['fileId']) {
            $response = new BinaryFileResponse("/tmp/testFile");

            return $response;
        }

        return new JsonResponse(['error' => 'file not found'], 404);
    }
}
