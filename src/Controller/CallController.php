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
        $call->setMessage($content['message']);
        $call->setApprovalType($content['approvalType']);
        $call->setToUser($this->em->getRepository(User::class)->find($content['to']));
        $call->setFromUser($this->userService->getUser());
        $call->setStatus(Call::STATUS_OPEN);


        $str = $this->generateRandomString(7);
        while ($this->function($str)) {
            $str = $this->generateRandomString(7);
        }
        $call->setLink($str);
        $this->em->persist($call);
        $this->em->flush();

        $tasks = $this->insertTasks($content['tasks'] ?? [], $call);
        $call->setTasks($tasks);

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

    private function insertTasks(array $tasksRequest, Call $call): array
    {
        $result = [];
        foreach ($tasksRequest as $tr) {
            $task = new Task();
            $task->setTitle($tr['title']);
            $task->setDescription($tr['description'] ?? null);
            $task->setAuthor($this->userService->getUser());
            $task->setTaskCall($call);
            $this->em->persist($task);
            $this->em->flush();
            $goals = $this->insertGoals($tr['goals'] ?? [], $task);
            $task->setGoals($goals);
            $result[] = $task;
        }
        return $result;
    }

    private function insertGoals(array $goalsArr, Task $task): array
    {
        $result = [];
        foreach ($goalsArr as $gr) {
            $goal = new Goal();
            $goal->setTitle($gr['title']);
            $goal->setTask($task);
            $goal->setStatus(Goal::STATUS_OPEN);
            $this->em->persist($goal);
            $this->em->flush();
            $result[] = $goal;
        }
        return $result;
    }

    private function function($chars): bool
    {
        return !!$this->em->getRepository(Call::class)->findOneBy([
            'link' => $chars
        ]);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
