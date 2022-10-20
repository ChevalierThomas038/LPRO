<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;

//#[Route('/security', name: 'app')]
class SecurityController extends AbstractController
{
    #[Route('/adduser', name: 'app_security')]
    public function addUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();

        $user = new User();
        $user->setUsername('Admin2')
            ->setName('Admin2')
            ->setRoles(['ROLE_ADMIN'])
            ->setBirthday(new \DateTime('2002-01-21'));

        $hashedPassword = $passwordHasher->hashPassword($user, 'Admin2');
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        return $this->render("<body></body>");
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
