<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    /** 
     * This controller display all ingredients
     * @param IngredientRepository $repository        
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * 
     */

    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render(
            'pages/ingredient/index.html.twig',
            [
                'ingredients' => $ingredients
            ]
        );
    }

    /** 
     * This controller add a new ingredient
     * @param EntityManagerInterface $manager        
     * @param IngredientType $form
     * @param Request $request
     * @return Response
     * 
     */

    #[Route('/ingredient/nouveau', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient à été créer avec succés !'
            );

            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /** 
     * This controller edit the ingredient
     * @param Ingredient $ingredient     
     * @param EntityManagerInterface $manager
     * @param IngredientType $form
     * @param Request $request
     * @return Response 
     */
    #[Route('/ingredient/edit/{id}', name: 'app_ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient à été modifié avec succés !'
            );

            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /** 
     * This controller delete the ingredient
     * @param Ingredient $ingredient     
     * @param EntityManagerInterface $manager
     * @return Response 
     */

    #[Route('/ingredient/suppression/{id}', name: 'app_ingredient_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {

        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient à été supprimé avec succés !'
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
