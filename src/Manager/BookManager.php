<?php

namespace App\Manager;

use App\Entity\Book;
use App\Model\GenericConstant;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class BookManager
{

    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public function handleBook(string $operation, ?Book $book): ?Book
    {
        match ($operation) {
            GenericConstant::PERSIST => $this->manager->persist($book),
            GenericConstant::FLUSH => $this->manager->flush(),
            GenericConstant::REMOVE => $this->manager->remove($book),
            GenericConstant::PERSIST_AND_FLUSH => $this->persistAndFlush($book),
            GenericConstant::REMOVE_AND_FLUSH => $this->removeAndFlush($book),
            default => throw new InvalidArgumentException('Invalid operation: '.$operation),
        };
        return $book;
    }

    private function persistAndFlush(Book $book): Book
    {
        $this->manager->persist($book);
        $this->manager->flush();
        return $book;
    }

    private function removeAndFlush(Book $book): Book
    {
        $this->manager->remove($book);
        $this->manager->flush();
        return $book;
    }

}