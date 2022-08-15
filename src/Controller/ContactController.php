<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Mail;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        $contact = new Contact();

        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre demande à bien été pris en compte !'
            );

            $content1 = 'Bonjour' . $contact->getFullName() . '<br>' . 'Merci pour votre demande, Vous allez recvoir une réponse dans les délais les plus brefs';
            $mail1 = new Mail();
            $mail1->send($contact->getEmail(), $contact->getFullName(), 'recontact Symrecipe!', $content1);
            // $mail2 = new Mail();
            // $mail2->send('admin@gmail.com', 'Email: ' . '<br>' . $contact->getEmail() . '<br>' . 'Email: ' . $contact->getFullName(), 'Sujet :.' . '<br>' . $contact->getSubject(), 'Message :.' . '<br>' . $contact->getMessage());
            return $this->redirectToRoute('app_contact');
        }
        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
