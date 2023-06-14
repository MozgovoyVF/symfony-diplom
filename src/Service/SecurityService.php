<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use Symfony\Component\Form\FormInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityService
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        Mailer $mailer,
        VerifyEmailHelperInterface $verifyEmailHelper,
        UserService $userService
    ) {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->userService = $userService;
    }

    /**
     * @param FormInterface $form
     * @return User
     */
    public function registerUser(FormInterface $form): User
    {
        /** @var UserRegistrationFormModel $userModel */
        $userModel = $form->getData();

        $user = new User();

        $user
            ->setEmail($userModel->email)
            ->setFirstName($userModel->firstName)
            ->setPassword($this->userService->passwordEncoder->hashPassword(
                $user,
                $userModel->plainPassword
            ))
            ->setSubscriptionDate(new \DateTime());;

        $this->userService->em->persist($user);
        $this->userService->em->flush();

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $this->mailer->sendWelcomeMail($user, $signatureComponents);

        return $user;
    }

    /**
   * @param string $uri
   * @param string $id
   * @param string $email
   * @return void
   */
    public function verifyEmail($uri, $id, $email): mixed
    {
        return $this->verifyEmailHelper->validateEmailConfirmation($uri, $id, $email);
    }
}
