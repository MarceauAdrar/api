<?php

namespace App\Controller;

use App\Repository\CenterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/v1/centers')]
class CenterController extends AbstractController
{
    #[Route('/', name: 'app_center_list')]
    public function index(CenterRepository $centerRepository, SerializerInterface $serializer): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => "Récupération de la liste des centres d'ABC Formation",
            'centers' => json_decode($serializer->serialize($centerRepository->findAll(), 'json'), true),
        ]);
    }
}
