<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('authors', name: 'all_authors', methods: ['GET'])]
    public function getBookList(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorsList = $authorRepository->findAll();
        $jsonBookList = $serializer->serialize($authorsList, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/authors/{id}', name: 'authors', methods: ['GET'])]
    public function getDetailBook(Author $author, SerializerInterface $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($author, 'json', ['groups' => 'getAuthors'] );
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
