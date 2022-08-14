<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\From\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * This controller allow us to edit user's profile
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]

    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {


        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Vous informations ont bien été modifiées. '
                );

                return $this->redirectToRoute("app_recipe");
            } else {
                $this->addFlash(
                    'warning',
                    'le mot de passe renseigné est incorrect. '
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to modify the passwordUs
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/utilisateur/edit-mdp/{id}', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function editPassword(
        User $choosenUser,
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager
    ): Response {


        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setcreatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($choosenUser);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'le mot de passe à bien été modifiée!'
                );

                return $this->redirectToRoute("app_recipe");
            } else {
                $this->addFlash(
                    'warning',
                    'le mot de passe renseigné est incorrect. '
                );
            }
        }

        return $this->render("pages/user/edit_password.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
