<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_account');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        $remember = $request->query->get('remember');
        return $this->render('templates/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'remember' => $remember]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    /**
     * @param Request $request
     * @param Mailer $mailer
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param UserService $userService
     */
    public function register(Request $request, Mailer $mailer, VerifyEmailHelperInterface $verifyEmailHelper, UserService $userService): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_account');
        }

        $form = $this->createForm(UserRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();

            $user = new User();

            $user
                ->setEmail($userModel->email)
                ->setFirstName($userModel->firstName)
                ->setPassword($userService->passwordEncoder->hashPassword(
                    $user,
                    $userModel->plainPassword
                ))
                ->setSubscriptionDate(new \DateTime());;

            $userService->em->persist($user);
            $userService->em->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $mailer->sendWelcomeMail($user, $signatureComponents);

            $this->addFlash('success', 'Для завершения регистрации подтвердите Ваш адрес электронной почты');

            return $this->render('templates/security/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        return $this->render('templates/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/verify', name: 'app_verify_email')]
    /**
     * @param Request $request
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserService $userService
     */
    public function verifyUserEmail(
        Request $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserService $userService
    ): Response {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();
        $this->addFlash('success', 'Аккаунт верифицирован!');

        $userService->authenticate($request, $user);

        return $this->render('templates/homepage.html.twig');
    }
}
