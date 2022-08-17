<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', 'Formulaire de contact');
        // Recupérer le formulaire
        $submitButton = $crawler->selectButton('Envoyer');
        $form = $submitButton->form();
        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "sssrt@symrecipe.fr";
        $form["contact[subject]"] = "test";
        $form["contact[message]"] = "test";
        // Soummetre le fomrulaire
        $client->submit($form);
        // Vérifier le status HTTP
        $this->assertResponseIsSuccessful(Response::HTTP_FOUND);
    }
}
