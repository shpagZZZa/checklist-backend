<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\Checklist;
use App\Entity\File;
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
     * @Route("/from-link/{uniqueId}", name="create-from-link", methods={"POST"})
     * @param Request $request
     * @param string $uniqueId
     * @return JsonResponse
     */
    public function createFromLinkAction(Request $request, string $uniqueId): JsonResponse
    {
        /** @var Checklist $entity */
        $entity = $this->em->getRepository(Checklist::class)->findOneBy([
            'unique_id' => $uniqueId
        ]);
        $checklist = $entity->jsonSerialize();
        $call = new Call();
        $call->setStatus(Call::STATUS_OPEN);
        $call->setToUser($this->userService->getUser());
        $call->setChecklist($entity);
        $this->em->persist($call);
        $this->em->flush();

        return new JsonResponse($call->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="get-user-calls", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserCalls(Request $request): JsonResponse
    {
        $user = $this->userService->getUser();
        $calls = $this->em->getRepository(Call::class)->findBy([
            'toUser' => $user,
            'status' => Call::STATUS_OPEN
        ]);

        $checklistsCreated = $this->em->getRepository(Checklist::class)->findBy([
            'author' => $user
        ]);
        $callsCreated = [];
        foreach ($checklistsCreated as $checklist) {
            $damn = $this->em->getRepository(Call::class)->findBy([
                'checklist' => $checklist,
                'status' => Call::STATUS_OPEN
            ]);
            $callsCreated = array_merge($callsCreated, $damn);
        }

        return new JsonResponse([
            'calls' => array_map(function (Call $call) {
                return $call->jsonSerialize();
            }, array_merge($callsCreated, $calls))
        ]);
    }
    /**
     * @Route("/check-user/{uniqueId}", name="check-user", methods={"GET"})
     * @param Request $request
     * @param string $uniqueId
     * @return JsonResponse
     */
    public function checkUserAction(Request $request, string $uniqueId): JsonResponse
    {
        $user = $this->userService->getUser();

        /** @var Checklist $entity */
        $checklistEntity = $this->em->getRepository(Checklist::class)->findOneBy([
            'unique_id' => $uniqueId
        ]);
        $entity = $this->em->getRepository(Call::class)->findOneBy([
            'toUser' => $user,
            'checklist' => $checklistEntity
        ]);
        return new JsonResponse([
            'status' => $entity ? 'created' : 'not_created'
        ], 200);
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
