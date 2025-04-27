<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;

class LivreController extends AbstractController
{
    private $entityManager;
    private $livreRepository;
    private $serializer;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        LivreRepository $livreRepository,
        SerializerInterface $serializer,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->livreRepository = $livreRepository;
        $this->serializer = $serializer;
        $this->security = $security;
    }

    /**
     * @Route("/livre", name="app_livre")
     */
    public function index(): Response
    {
        return $this->render('livre/index.html.twig', [
            'controller_name' => 'LivreController',
        ]);
    }

    /**
     * @Route("/apiplatform/adherents/livres", name="api_livres_adherents", methods={"GET"})
     */
    public function getLivresForAdherents(Request $request): JsonResponse
    {
        // Vérifier si l'utilisateur a le rôle ROLE_ADHERENT
        if (!$this->security->isGranted('ROLE_ADHERENT')) {
            return new JsonResponse(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        // Récupérer tous les livres
        $livres = $this->livreRepository->findAll();

        // Sérialiser les livres avec le groupe de normalisation 'get_role_adherent'
        $jsonContent = $this->serializer->serialize($livres, 'json', ['groups' => 'get_role_adherent']);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
