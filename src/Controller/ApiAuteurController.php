<?php
namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuteurController extends AbstractController
{
    /**
     * @Route("/api/auteurs", name="api_auteurs", methods={"GET"})
     */
    public function list(AuteurRepository $repo, SerializerInterface $serializer): Response
    {
        $auteurs = $repo->findAll();
        $resultat = $serializer->serialize(
            $auteurs,
            'json',
            ['groups' => ['listAuteurFull']]
        );
        return new JsonResponse($resultat, 200, [], true);
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_show", methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serializer): Response
    {
        $resultat = $serializer->serialize(
            $auteur,
            'json',
            ['groups' => ['listAuteurSimple']]
        );
        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/auteurs", name="api_auteurs_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $manager, NationaliteRepository $repoNation, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();
        $dataTab = json_decode($data, true);

        // Vérifiez que les données nécessaires sont présentes
        if (!isset($dataTab['nom']) || !isset($dataTab['prenom']) || !isset($dataTab['nationalite']['id'])) {
            return new JsonResponse("Données invalides", Response::HTTP_BAD_REQUEST, [], true);
        }

        $nationalite = $repoNation->find($dataTab['nationalite']['id']);
        if (!$nationalite) {
            return new JsonResponse("Nationalité non trouvée", Response::HTTP_BAD_REQUEST, [], true);
        }

        $auteur = $serializer->deserialize($data, Auteur::class, 'json');
        $auteur->setNationalite($nationalite);

        $errors = $validator->validate($auteur);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($auteur);
        $manager->flush();

        return new JsonResponse(
            "L'auteur a bien été créé",
            Response::HTTP_CREATED,
            ["location" => $this->generateUrl(
                'api_auteurs_show',
                ['id' => $auteur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true
        );
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_delete", methods={"DELETE"})
     */
    public function delete(Auteur $auteur, EntityManagerInterface $manager): Response
    {
        $manager->remove($auteur);
        $manager->flush();

        return new JsonResponse("L'auteur a bien été supprimé", Response::HTTP_OK, [], false);
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_edit", methods={"PUT"})
     */
    public function edit(Auteur $auteur, NationaliteRepository $repoNation, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();
        $dataTab = json_decode($data, true);

        // Vérifiez que les données nécessaires sont présentes
        if (!isset($dataTab['nationalite']['id'])) {
            return new JsonResponse("Données invalides", Response::HTTP_BAD_REQUEST, [], true);
        }

        $nationalite = $repoNation->find($dataTab['nationalite']['id']);
        if (!$nationalite) {
            return new JsonResponse("Nationalité non trouvée", Response::HTTP_BAD_REQUEST, [], true);
        }

        // Désérialiser les données dans l'objet existant
        $auteur = $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate' => $auteur]);
        $auteur->setNationalite($nationalite);

        $errors = $validator->validate($auteur);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($auteur);
        $manager->flush();

        return new JsonResponse("L'auteur a bien été modifié", Response::HTTP_OK, [], false);
    }
}
