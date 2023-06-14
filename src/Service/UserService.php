<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserService
{
    /**
     * @var UserPasswordHasherInterface
     */
    public $passwordEncoder;
    /**
     * @var UserAuthenticatorInterface
     */
    public $authenticator;
    /**
     * @var LoginFormAuthenticator
     */
    public $formAuthenticator;
    /**
     * @var EntityManagerInterface
     */
    public $em;
    /**
     * @var User
     */
    public $user;
    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param UserAuthenticatorInterface $authenticator
     * @param LoginFormAuthenticator $formAuthenticator
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserPasswordHasherInterface $passwordEncoder,
        UserAuthenticatorInterface $authenticator,
        LoginFormAuthenticator $formAuthenticator,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
        $this->formAuthenticator = $formAuthenticator;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     * @return User
     */
    public function create($data): User
    {
        $this->user = new User();
        $this->user
            ->setEmail($data('email'))
            ->setFirstName($data('firstName'))
            ->setPassword($this->passwordEncoder->hashPassword($this->user, $data('password')));

        $this->em->persist($this->user);
        $this->em->flush();

        return $this->user;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function authenticate(Request $request, $user = null): Response
    {
        $user = $user ? $user : $this->user;

        $this->em->flush();

        return $this->authenticator->authenticateUser(
            $user,
            $this->formAuthenticator,
            $request
        );
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findUser($id): User
    {
        return $this->userRepository->find($id);
    }
}
