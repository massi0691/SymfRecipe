<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredeintSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManger = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManger->find(User::class, 1);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient_new'));
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "ingrédient5",
            'ingredient[price]' => 35.00
        ]);
        $client->submit($form);


        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient à été créer avec succés !');
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfListingredientIsSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManger = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManger->find(User::class, 1);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient'));
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfUpdateIningredientIsSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManger = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManger->find(User::class, 1);
        $ingredient = $entityManger->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient_edit', ['id' => $ingredient->getId()]));
        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "ingrédient6",
            'ingredient[price]' => 37.00
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient à été modifié avec succés !');
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfDeleteInIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient_delete', ['id' => $ingredient->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient à été supprimé avec succés !');
        $this->assertRouteSame('app_ingredient');
    }
}
