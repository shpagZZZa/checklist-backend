<?php

namespace App\Service\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;


class UserService
{
    private EntityManagerInterface $em;

    private const SALT_LENGTH = 4;
    private const ID_FIELD = 'email';

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        if (!!$this->findUser($user->getEmail())) {
            throw new NonUniqueResultException();
        }

        $passwordRaw = $data['password'];
        $salt = $this->generateSalt();
        $passwordHashed = $this->encodePassword($passwordRaw, $salt);
        $user->setPassword($passwordHashed);
        $user->setSalt($salt);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param string $identifier
     * @return User|null
     */
    public function findUser(string $identifier): ?User
    {
        return $this->em->getRepository(User::class)
            ->findOneBy([
                self::ID_FIELD => $identifier
            ]);
    }

    /**
     * @param string $raw
     * @param string $salt
     * @return string
     */
    public function encodePassword(string $raw, string $salt): string
    {
        return md5(md5($raw . $salt));
    }

    /**
     * @return string
     */
    private function generateSalt(): string
    {
        return substr(md5(microtime()), rand(0, 26), self::SALT_LENGTH);
    }
}