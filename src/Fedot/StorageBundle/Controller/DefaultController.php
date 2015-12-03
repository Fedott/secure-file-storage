<?php

namespace Fedot\StorageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('StorageBundle:Default:index.html.twig');
    }

    /**
     * @Route("/upload", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $bodyResource = $request->getContent(true);

        file_put_contents('/tmp/testFile', $bodyResource);

        return new JsonResponse([
            'fileId' => 'qweqwe',
        ], 201);
    }

    /**
     * @Route("/download", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function downloadAction(Request $request)
    {
        $jsonBody = json_decode($request->getContent(), true);

        if ("qweqwe" == $jsonBody['fileId']) {
            $response = new BinaryFileResponse("/tmp/testFile");

            return $response;
        }

        return new JsonResponse(['error' => 'file not found'], 404);
    }
}
