<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Model\AuthorConstant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class BookFixtures extends Fixture implements DependentFixtureInterface
{

    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle($this->faker->sentence());
            $book->setCoverText($this->faker->paragraph());
            $book->setComment($this->faker->text());
            $book->setAuthor($this->getReference(AuthorConstant::AUTHORS_FIXTURES_REFERENCE.rand(0, 9)));
            $manager->persist($book);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthorFixtures::class,
        ];
    }

}