<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $button = $crawler->filter('.btn.btn-primary');
        $this->assertEquals(1, count($button));
        $recipes = $crawler->filter('.recipes .card ');
        $this->assertEquals(3, count($recipes));
        $this->assertSelectorTextContains('h1', 'Bienvenue sur SymRecipe!');
    }
}
