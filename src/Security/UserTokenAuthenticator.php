<?php

namespace App\Security;

use App\Entity\User;
use App\Service\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class UserTokenAuthenticator extends AbstractGuardAuthenticator
{
    private TokenService $tokenService;
    private UserService $userService;
    private EntityManagerInterface $em;

    private const TOKEN_LIFETIME = 3600;

    /**
     * @param TokenService $tokenService
     * @param UserService $userService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        TokenService $tokenService,
        UserService $userService,
        EntityManagerInterface $em
    ) {
        $this->tokenService = $tokenService;
        $this->userService = $userService;
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message'   =>  'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get('Authorization') ?? $request->headers->get('Authorization')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'];
        if (!$token) {
            throw new AuthenticationException('передай токен');
        }

        $data = $this->tokenService->decode($token);

        if (!$data) {
            throw new AuthenticationException('некорректный токен');
        }

        if (time() > $data['createdAt'] + self::TOKEN_LIFETIME) {
            throw new AuthenticationException('истекло время жизни токена');
        }

        $user = $this->em->getRepository(User::class)->find($data['userId']);

        if (!$user) {
            throw new AuthenticationException('хуйня какая то');
        }

        $this->userService->setUser($user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message'   =>  $exception->getMessage()
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
