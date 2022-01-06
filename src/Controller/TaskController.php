<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskUserRel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task", name="task_")
 */
class TaskController extends BaseController
{
    /**
     * @Route("/{id}", name="edit", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function editAction(Request $request, int $id): JsonResponse
    {
        /** @var Task $task */
        $task = $this->em->getRepository(Task::class)->find($id);

        $content = json_decode($request->getContent(), true);
        $title = $content['title'] ?? null;

        if ($title) {
            $task->setTitle($title);
        }
        $this->em->persist($task);
        $this->em->flush($task);
        return new JsonResponse($task->jsonSerialize());
    }

    /**
     * @Route("/change-status/{id}", name="change-status", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function changeStatusAction(Request $request, int $id): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $status = $content['status'];
        /** @var Task $task */
        $task = $this->em->getRepository(Task::class)->find($id);
        $user = $this->userService->getUser();
        /** @var TaskUserRel $relItem */
        $relItem = $this->em->getRepository(TaskUserRel::class)->findOneBy([
            'user' => $user,
            'task' => $task
        ]);
        if (!$relItem) {
            $relItem = new TaskUserRel();
            $relItem->setUser($user);
            $relItem->setTask($task);
        }
        $relItem->setStatus($status);
        $this->em->persist($relItem);
        $this->em->flush();
        return new JsonResponse($this->setTaskStatus($task)->jsonSerialize());
    }
}
