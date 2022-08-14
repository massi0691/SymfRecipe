<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_security_login', methods: ['GET', 'POST'])]
    /**
     * This Controller allow us to login
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
    #[Route('/deconnexion', name: 'app_security_logout')]
    /**
     * This Controller allow us to logout
     *
     * @return void
     */
    public function logout()
    {
        // nothing to do here 
    }

    #[Route('/inscription', name: 'app_security_register',  methods: ['GET', 'POST'])]
    /**
     * This Controller allow us to register
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->addFlash(
                'success',
                'Votre compte à bien été crée.'

            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("app_security_login");
        }
        return $this->render("pages/security/register.html.twig", [
            'form' => $form->createView()

        ]);
    }
}
