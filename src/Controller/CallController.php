<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\Goal;
use App\Entity\Task;
use App\Entity\User;
use App\Security\UserService;
use App\Service\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/call", name="call_")
 */
class CallController extends BaseController
{
    /**
     * @Route("", name="create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $call = new Call();

        return new JsonResponse($call->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="get", methods={"GET"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getAction(Request $request, int $id): JsonResponse
    {
        /** @var Call $entity */
        $entity = $this->em->getRepository(Call::class)->find($id);
        if (!$entity) {
            return new JsonResponse(['not found)'], 404);
        }
        return new JsonResponse($entity->jsonSerialize(), 200);
    }
}
