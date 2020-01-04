<?php

namespace App\UI\Controller\Security;

use App\Domain\Entity\User;
use App\UI\Form\RegistrationFormType;
use App\Framework\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function form(Request $request): Response
    {
        if ($request->attributes->get('action', 'login') == 'login') {
            $template = 'security/login.html.twig';
        } else {
            $template = 'security/register.html.twig';
        }

        return $this->prepareForm($template);
    }

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->prepareForm('security/register.html.twig', $form);
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->prepareForm('security/login.html.twig', null, $lastUsername, $error);
    }

    private function prepareForm($template, $form = null, $username = null, $error = null): Response
    {
        if (!isset($form)) {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
        }

        return $this->render($template, [
            'last_username' => $username,
            'error' => $error,
            'registrationForm' => $form->createView()
        ]);

    }

    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
