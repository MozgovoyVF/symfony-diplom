<?php

namespace App\Service;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserService
{
  public $passwordEncoder;
  public $authenticator;
  public $formAuthenticator;
  public $em;
  public $user;

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
    EntityManagerInterface $em
  ) {
    $this->passwordEncoder = $passwordEncoder;
    $this->authenticator = $authenticator;
    $this->formAuthenticator = $formAuthenticator;
    $this->em = $em;
  }

  /**
   * @param Request $request
   */
  public function create(Request $request)
  {
    $this->user = new User();
    $this->user
      ->setEmail($request->request->get('email'))
      ->setFirstName($request->request->get('firstName'))
      ->setPassword($this->passwordEncoder->hashPassword($this->user, $request->request->get('password')));

    $this->em->persist($this->user);
    $this->em->flush();

    return $this;
  }

  /**
   * @param Request $request
   */
  public function authenticate(Request $request, $user = null)
  {
    $user = $user ? $user : $this->user;

    return $this->authenticator->authenticateUser(
      $user,
      $this->formAuthenticator,
      $request
    );
  }
}
