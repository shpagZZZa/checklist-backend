<?php

namespace App\Controller;


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

}
