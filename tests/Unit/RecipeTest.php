<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recette;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{

   public function myEntity():Recette
    {
    $recette= new Recette();
    $recette->setName('Recipe #1')
        ->setDescription('Description # 1')
        ->setIsFavorite(true)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setUpdatedAtValue(new \DateTimeImmutable());
    return $recette;
    }


    public function testSomething(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $recette = $this->myEntity();
        $errors = $container->get('validator')->validate($recette);

        $this->assertCount(0,$errors);
       
    }

    public function testInvalidName()
    {
        self:: bootkernel();
        $container = static::getContainer();
        $recette = $this->myEntity();
        $recette->setName('');
        $errors = $container->get('validator')->validate($recette);

        $this->assertCount(2,$errors);
       
    }

    public function testGetAverage()
    {
        self:: bootkernel();
        $container = static::getContainer();
        $recette = $this->myEntity();
        $user = $container->get('doctrine.orm.entity_manager')->find(User::class,1);
        for ($i=0; $i <5 ; $i++) { 
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recette);

            $recette->addMark($mark);
        }

        $this->assertTrue(2.0 === $recette->getAverage());
       
    }
}
