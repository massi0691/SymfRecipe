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
        $this->assertSelectorTextContains('h4', 'Hello World');
        // Recupérer le formulaire
        $submitButton = $crawler->selectButton('Envoyer');
        $form = $submitButton->form();
        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@symrecipe.fr";
        $form["contact[subject]"] = "test";
        $form["contact[message]"] = "test";
        // Soummetre le fomrulaire
        $client->submit($form);
        // Vérifier le status HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        //Vérifier l'envoie de mail
        $this->assertEmailCount(1);
        $client->followRedirect();
        // Vérifier le message de succés
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'Votre demande à bien été prise en compte !'
        );
    }
}
