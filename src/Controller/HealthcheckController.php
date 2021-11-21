<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthcheckController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/healthcheck", name="healthcheck")
     */
    public function healthcheck(): JsonResponse
    {
        return new JsonResponse([
            'checklist-backend' => 'ok'
        ], Response::HTTP_OK);
    }
}