<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Manager\BookManager;
use App\Model\GenericConstant;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/', name: 'books.')]
class BookController extends AbstractController
{
    #[Route('books', name: 'all', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('books/{id}', name: 'detail', methods: ['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('books/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteBook(Book $book, BookManager $bookManager): JsonResponse
    {
        $bookManager->handleBook(GenericConstant::REMOVE_AND_FLUSH, $book);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('books', name: 'add', methods: ['POST'])]
    public function createBook(
        Request $request,
        SerializerInterface $serializer,
        BookManager $bookManager,
        UrlGeneratorInterface $urlGenerator,
        AuthorRepository $authorRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;
        $book->setAuthor($authorRepository->find($idAuthor));

        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }
        $bookManager->handleBook(GenericConstant::PERSIST_AND_FLUSH, $book);
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator->generate('books.detail', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('books/{id}', name: 'update', methods: ['PUT'])]
    public function updateBook(
        Request $request,
        SerializerInterface $serializer,
        Book $currentBook,
        BookManager $bookManager,
        AuthorRepository $authorRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $updatedBook = $serializer->deserialize(
            $request->getContent(),
            Book::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]
        );
        $errors = $validator->validate($updatedBook);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;
        $updatedBook->setAuthor($authorRepository->find($idAuthor));
        $bookManager->handleBook(GenericConstant::PERSIST_AND_FLUSH, $updatedBook);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
