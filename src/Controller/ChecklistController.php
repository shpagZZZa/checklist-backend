<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\CallFileRel;
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
 * @Route("/checklist", name="checklist_")
 */
class ChecklistController extends BaseController
{

    /**
     * @Route("/{id}/upload", name="upload", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function uploadAction(Request $request, int $id): JsonResponse
    {
        $base64 = json_decode($request->getContent(), true)['base64'];
        $title = json_decode($request->getContent(), true)['title'];

        /** @var Checklist $checklist */
        $checklist = $this->em->getRepository(Checklist::class)->find($id);

        /** @var Call $call */
        $call = $this->em->getRepository(Call::class)->findOneBy([
            'checklist' => $checklist,
            'toUser' => $this->userService->getUser()
        ]);

        $file = new File();
        $file->setTitle($title);
        $file->setBase64($base64);
        $file->setAuthor($this->userService->getUser());

        $this->em->persist($file);
        $this->em->flush();

        $rel = new CallFileRel();
        $rel->setCall($call);
        $rel->setFile($file);
        $rel->setAuthor($this->userService->getUser());


        $this->em->persist($rel);
        $this->em->flush();

        return new JsonResponse($file->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}/files", name="get-files", methods={"GET"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getFilesAction(Request $request, int $id): JsonResponse
    {
        /** @var Checklist $checklist */
        $checklist = $this->em->getRepository(Checklist::class)->find($id);

        $this->em->clear(User::class);
        /** @var Call[] $calls */
        $calls = $this->em->getRepository(Call::class)->findBy([
            'checklist' => $checklist,
        ]);

        $files = [];
        /** @var CallFileRel[] $rels */
        $rels = $this->em->getRepository(CallFileRel::class)->findBy([
            'call' => $calls
        ]);
        foreach ($rels as $rel) {
            $r = $rel->getFile()->jsonSerialize();
            $r['author'] = $rel->getFile()->getAuthor()->jsonSerialize();
            $files[] = $r;
        }
        return new JsonResponse($files, 200);
    }
    /**
     * @Route("/enemy-status/{userId}/{id}", name="enemy-status", methods={"GET"})
     * @param Request $request
     * @param int $userId
     * @param int $id
     * @return JsonResponse
     */
    public function enemyStatusAction(Request $request, int $userId, int $id): JsonResponse
    {
        /** @var Checklist $entity */
        $entity = $this->em->getRepository(Checklist::class)->find($id);
        if (!$entity) {
            return new JsonResponse(['not found)'], 404);
        }
        /** @var Task $task */
        foreach ($entity->getTasks() as $task) {
            $this->setTaskStatus($task, $userId);
        }
        return new JsonResponse($entity->jsonSerialize());
    }

    /**
     * @Route("/", name="all-checklists", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function allChecklistsAction(Request $request): JsonResponse
    {
        return new JsonResponse(array_map(function (Checklist $checklist) {
            return $checklist->jsonSerialize();
        }, $this->em->getRepository(Checklist::class)->findBy([
            'author' => $this->userService->getUser()
        ])));
    }

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
        /** @var Task $task */
        foreach ($entity->getTasks() as $task) {
            $this->setTaskStatus($task);
        }
        return new JsonResponse($entity->jsonSerialize());
    }

    /**
     * @Route("/{id}", name="edit", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function editAction(Request $request, int $id): JsonResponse
    {
        /** @var Checklist $checklist */
        $checklist = $this->em->getRepository(Checklist::class)->find($id);
        if (!$checklist) {
            return new JsonResponse(['not found)'], 404);
        }

        $content = json_decode($request->getContent(), true);
        $title = $content['title'] ?? null;
        $approvalType = $content['approvalType'] ?? null;

        if ($title) {
            $checklist->setTitle($title);
        }
        if ($approvalType) {
            $checklist->setApprovalType($approvalType);
        }
        $this->em->persist($checklist);
        $this->em->flush($checklist);
        return new JsonResponse($checklist->jsonSerialize());
    }

    /**
     * @Route("/{id}/add-task", name="add-task", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addTaskAction(Request $request, int $id): JsonResponse
    {
        /** @var Checklist $checklist */
        $checklist = $this->em->getRepository(Checklist::class)->find($id);
        $content = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($content['title']);
        $task->setDescription($content['description'] ?? null);
        $task->setAuthor($this->userService->getUser());
        $task->setChecklist($checklist);
        $this->em->persist($task);
        $this->em->flush();
        $goals = $this->insertGoals($content['goals'] ?? [], $task);
        $task->setGoals($goals);
        $this->setTaskStatus($task);

        return new JsonResponse($checklist->jsonSerialize(), 200);
    }

    /**
     * @Route("/{id}/remove-task/{taskId}", name="remove-task", methods={"DELETE"})
     * @param Request $request
     * @param int $id
     * @param int $taskId
     * @return JsonResponse
     */
    public function removeTaskAction(Request $request, int $id, int $taskId): JsonResponse
    {
        /** @var Checklist $checklist */
        $checklist = $this->em->getRepository(Checklist::class)->find($id);
        /** @var Task $task */
        $task = $this->em->getRepository(Task::class)->find($taskId);
        $task->setChecklist(null);
        $this->em->persist($task);
        $this->em->flush();

        return new JsonResponse($checklist->jsonSerialize(), 200);
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
        $checklist->setTitle($content['title']);
        $checklist->setAuthor($this->userService->getUser());


        $str = $this->generateRandomString(7);
        while ($this->function($str)) {
            $str = $this->generateRandomString(7);
        }
        $checklist->setUniqueId($str);
        $checklist->setApprovalType($content['approvalType'] ?? 'default');
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
            $this->em->persist($task);
            $this->em->flush();
            $goals = $this->insertGoals($tr['goals'] ?? [], $task);
            $task->setGoals($goals);
            $this->setTaskStatus($task);
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
