<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\Checklist;
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
 * @Route("/checklist", name="checklist_")
 */
class ChecklistController extends BaseController
{

    /**
     * @Route("/{id}", name="get", methods={"GET"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getAction(Request $request, int $id): JsonResponse
    {
        /** @var Checklist $entity */
        $entity = $this->em->getRepository(Checklist::class)->find($id);
        if (!$entity) {
            return new JsonResponse(['not found)'], 404);
        }
        return new JsonResponse($entity->jsonSerialize());
    }

    /**
     * @Route("", name="create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $checklist = new Checklist();
        $checklist->setMessage($content['message']);
        $checklist->setAuthor($this->userService->getUser());


        $str = $this->generateRandomString(7);
        while ($this->function($str)) {
            $str = $this->generateRandomString(7);
        }
        $checklist->setUniqueId($str);
        $this->em->persist($checklist);
        $this->em->flush();

        $tasks = $this->insertTasks($content['tasks'] ?? [], $checklist);
        $checklist->setTasks($tasks);

        return new JsonResponse($checklist->jsonSerialize(), Response::HTTP_CREATED);
    }

    private function insertTasks(array $tasksRequest, Checklist $checklist): array
    {
        $result = [];
        foreach ($tasksRequest as $tr) {
            $task = new Task();
            $task->setTitle($tr['title']);
            $task->setDescription($tr['description'] ?? null);
            $task->setAuthor($this->userService->getUser());
            $task->setChecklist($checklist);
            $task->setStatus(Task::STATUS_OPEN);
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
        return !!$this->em->getRepository(Checklist::class)->findOneBy([
            'unique_id' => $chars
        ]);
    }

    private function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
