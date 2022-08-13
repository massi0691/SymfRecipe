<?php


namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', 'app_home', methods: ['GET'])]

    public function index(RecetteRepository $repository): Response
    {

        return $this->render('pages/home.html.twig', [
            'recipes' => $repository->findPublicRecipe(3)
        ]);
    }
}
