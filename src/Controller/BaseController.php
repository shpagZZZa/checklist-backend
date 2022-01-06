<?php

namespace App\Controller;


use App\Entity\Task;
use App\Entity\TaskUserRel;
use App\Security\UserService;
use App\Service\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected UserService $userService;
    protected EntityManagerInterface $em;
    protected TokenService $tokenService;

    /**
     * @param UserService $userService
     * @param TokenService $tokenService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserService $userService,
        TokenService $tokenService,
        EntityManagerInterface $em
    ) {
        $this->userService = $userService;
        $this->em = $em;
        $this->tokenService = $tokenService;
    }

    protected function setTaskStatus(Task $task): Task
    {
        $user = $this->userService->getUser();
        /** @var TaskUserRel $relItem */
        $relItem = $this->em->getRepository(TaskUserRel::class)->findOneBy([
            'user' => $user,
            'task' => $task
        ]);
        if (!$relItem) {
            $task->setStatus(Task::STATUS_OPEN);
        } else {
            $task->setStatus($relItem->getStatus());
        }
        return $task;
    }
}
