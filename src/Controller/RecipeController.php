<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecipeType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Recipe;

class RecipeController extends AbstractController
{
    /** 
     * This controller display all recipes
     * @param IngredientRepository $repository        
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'app_recipe', methods: ["GET"])]
    public function index(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route("/recette/publique", name: "app_recipe_public", methods: ['GET'])]
    public function indexPublic(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render("pages/recipe/index_public.html.twig", [
            'recipes' => $recipes
        ]);
    }

    /**
     * This Controller allow us to see recipe if this one is public
     *
     * @param Recette $recipe
     * @return Response
     */

    #[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true ")]
    #[Route("/recette/{id}", name: "app_recipe_show", methods: ['GET'])]

    public function show(Recette $recipe): Response
    {
        return $this->render("pages/recipe/show.html.twig", [
            'recipe' => $recipe
        ]);
    }


    /**
     * This Controller  add new recipe
     * @param EntityManagerInterface $manager       
     * @param Request $request
     * @return Response
     */
    #[Route('/recette/creation', name: 'app_recipe_new', methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette = $form->getData();
            $manager->persist($recette);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette à été créer avec succés !'
            );
            return $this->redirectToRoute("app_recipe");
        }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/recette/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    /**
     * This Controller allow us to edit the Recipe
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Recette $recette, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette à été modifié avec succés !'
            );

            return $this->redirectToRoute('app_recipe');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/suppression/{id}', name: 'app_recipe_delete', methods: ['GET'])]
    /**
     * This controller allow us to delete recipe selected
     *
     * @param EntityManagerInterface $manager
     * @param Recette $recette
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Recette $recette): Response
    {

        $manager->remove($recette);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette à été supprimé avec succés !'
        );

        return $this->redirectToRoute('app_recipe');
    }
}
