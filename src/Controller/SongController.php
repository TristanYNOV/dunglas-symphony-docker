<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use App\Traits\StatsPropertiesTraits;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class SongController extends AbstractController 
{
    use StatsPropertiesTraits;

    #[Route('api/v1/song', name: 'get_all_song', methods: ['GET'])]
    public function getAll(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $songRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json');
        return new JsonResponse(data: $jsonData, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('api/v1/song/{id}', name: 'get_song', methods: ['GET'])]
    public function get(Song $id ,SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($id, 'json');
        return new JsonResponse(data: $jsonData, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('api/v1/song', name: 'create_song', methods: ['POST'])]
    public function createSong(Request $request, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json');
        $song->setName(name: $song->getName() ?? 'Nullable name');
        $entityManager->persist(object: $song);
        $entityManager->flush();
        $jsonData = $serializer->serialize($song, 'json');
        $location = $urlGenerator->generate(name:'get_song', parameters: ["id" => $song->getId()], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(data: $jsonData, status: Response::HTTP_CREATED, headers: ["location" => $location], json: true);
    }

    #[Route('api/v1/song/{id}', name: 'update_song', methods: ['PATCH'])]
    public function updateSong(Song $id ,Request $request, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $id]);
        $entityManager->persist(object: $song);
        $entityManager->flush();
        $jsonData = $serializer->serialize($song, 'json');
        $location = $urlGenerator->generate(name:'get_song', parameters: ["id" => $song->getId()], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    #[Route('api/v1/song/{id}', name: 'delete_song', methods: ['DELETE'])]
    public function deleteSong(Song $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove(object: $id);
        $entityManager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
