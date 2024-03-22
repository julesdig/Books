<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Manager\AuthorManager;
use App\Model\GenericConstant;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/', name: 'authors.')]
class AuthorController extends AbstractController
{
    #[Route('authors', name: 'all', methods: ['GET'])]
    public function getAuthorsList(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorsList = $authorRepository->findAll();
        $jsonBookList = $serializer->serialize($authorsList, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('authors/{id}', name: 'detail', methods: ['GET'])]
    public function getAuthorBook(Author $author, SerializerInterface $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('authors/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, AuthorManager $authorManager): JsonResponse
    {
        $authorManager->handleAuthor(GenericConstant::REMOVE_AND_FLUSH, $author);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/authors', name: 'create', methods: ['POST'])]
    public function createAuthor(
        Request $request,
        SerializerInterface $serializer,
        AuthorManager $authorManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');
        $errors = $validator->validate($author);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $authorManager->handleAuthor(GenericConstant::PERSIST_AND_FLUSH, $author);

        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);
        $location = $urlGenerator->generate('authors.detail', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/authors/{id}', name: "updateAuthors", methods: ['PUT'])]
    public function updateAuthor(
        Request $request,
        SerializerInterface $serializer,
        Author $currentAuthor,
        AuthorManager $authorManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $updatedAuthor = $serializer->deserialize(
            $request->getContent(),
            Author::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]
        );
        $errors = $validator->validate($currentAuthor);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $authorManager->handleAuthor(GenericConstant::PERSIST_AND_FLUSH, $updatedAuthor);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
