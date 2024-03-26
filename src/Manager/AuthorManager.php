<?php

namespace App\Manager;

use App\Entity\Author;
use App\Model\GenericConstant;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class AuthorManager
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public function handleAuthor(string $operation, ?Author $author): ?Author
    {
        match ($operation) {
            GenericConstant::PERSIST => $this->manager->persist($author),
            GenericConstant::FLUSH => $this->manager->flush(),
            GenericConstant::REMOVE => $this->manager->remove($author) ,
            GenericConstant::PERSIST_AND_FLUSH => $this->persistAndFlush($author),
            GenericConstant::REMOVE_AND_FLUSH => $this->removeAndFlush($author),
            default => throw new InvalidArgumentException('Invalid operation: ' . $operation),
        };
        return $author;
    }
    private function persistAndFlush(Author $author): Author
    {
        $this->manager->persist($author);
        $this->manager->flush();
        return $author;
    }
    private function removeAndFlush(Author $author): Author
    {
        $this->manager->remove($author);
        $this->manager->flush();
        return $author;
    }
}
