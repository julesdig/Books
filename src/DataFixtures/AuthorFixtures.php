<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Model\AuthorConstant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AuthorFixtures extends Fixture
{
    private Generator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $author = new Author();
            $author->setFirstName($this->faker->firstName);
            $author->setLastName($this->faker->lastName);
            $manager->persist($author);
            $this->addReference(AuthorConstant::AUTHORS_FIXTURES_REFERENCE.$i, $author);
        }
        $manager->flush();
    }
}
