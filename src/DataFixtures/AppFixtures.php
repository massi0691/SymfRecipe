<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recette;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;


class AppFixtures extends Fixture
{
    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {

        // Users
        $users = [];
        for ($z = 1; $z <= 10; $z++) {
            $user = new User();

            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE USER'])
                ->setPlainPassword('password');
            $users[] = $user;
            $manager->persist($user);
        }




        // Ingredients
        $ingredient = [];
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100))
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recettes
        $recettes = [];
        for ($j = 1; $j <= 25; $j++) {

            $recette = new Recette();

            $recette->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false);
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recette->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recettes[] = $recette;
            $manager->persist($recette);
        }






        // set Marks

        foreach ($recettes as $recette) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recette);
                $manager->persist($mark);
            }
        }


        // Contact 

        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . $i + 1)
                ->setMessage($this->faker->text());

            $manager->persist($contact);
        }


        $manager->flush();
    }
}
