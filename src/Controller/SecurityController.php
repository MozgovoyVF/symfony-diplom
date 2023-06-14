<?php

namespace App\Controller;

use App\Form\UserRegistrationFormType;
use App\Service\SecurityService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
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

    /**
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param SecurityService $securityService
     * @return Response
     */
    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, SecurityService $securityService): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_account');
        }

        $form = $this->createForm(UserRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $securityService->registerUser($form);

            $this->addFlash('success', 'Для завершения регистрации подтвердите Ваш адрес электронной почты');
        }

        return $this->render('templates/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param UserService $userService
     * @param SecurityService $securityService
     * @return Response
     */
    #[Route(path: '/verify', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserService $userService,
        SecurityService $securityService
    ): Response {
        $user = $userService->findUser($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $securityService->verifyEmail($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $this->addFlash('success', 'Аккаунт верифицирован!');

        $userService->authenticate($request, $user);

        return $this->render('templates/homepage.html.twig');
    }
}
