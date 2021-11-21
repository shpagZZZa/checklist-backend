<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\User;
use App\Service\Security\UserService;
use App\Service\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends BaseController
{
    private UserService $userService;
    private TokenService $tokenService;
    private EntityManagerInterface $em;

    /**
     * @param UserService $userService
     * @param EntityManagerInterface $em
     * @param TokenService $tokenService
     */
    public function __construct(
        UserService $userService,
        EntityManagerInterface $em,
        TokenService $tokenService
    ) {
        $this->userService = $userService;
        $this->em = $em;
        $this->tokenService = $tokenService;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true);
        try {
            $user = $this->userService->createUser($body);
        } catch (NonUniqueResultException $e) {
            return new JsonResponse(['error' => 'Пользователь с таким e-mail уже существует'], Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
        }
        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true);
        $email = $body['email'];
        $password = $body['password'];
        $user = $this->userService->findUser($email);
        if (!$user) {
            return new JsonResponse(['not found bro'], Response::HTTP_NOT_FOUND);
        }

        if ($this->userService->encodePassword($password, $user->getSalt()) !== $user->getPassword()) {
            return new JsonResponse(['error' => 'неверный пароль'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'userId' => $user->getId(),
            'createdAt' => time()
        ];

        $token = $this->tokenService->encode($data);
        return new JsonResponse($token, Response::HTTP_OK);
    }
}