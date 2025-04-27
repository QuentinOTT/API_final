<?php

namespace App\Serializer;

use App\Entity\Pret;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Context builder pour l'entité Pret qui gère la visibilité des données selon le rôle de l'utilisateur.
 * Ce context builder est similaire au LivreContextBuilder mais adapté pour l'entité Pret.
 */
final class PretContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    /**
     * Constructeur qui reçoit le decorateur et le checker d'autorisation.
     * Le decorateur permet d'appeler le context builder original d'API Platform.
     */
    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Méthode principale qui modifie le contexte de sérialisation selon le rôle de l'utilisateur.
     * Elle est appelée automatiquement par API Platform lors de la sérialisation d'un objet.
     *
     * @param Request $request La requête HTTP actuelle
     * @param bool $normalization Si c'est une opération de normalisation (GET) ou dénormalisation (POST/PUT)
     * @param array|null $extractedAttributes Les attributs extraits de l'annotation @ApiResource
     * @return array Le contexte modifié
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        // Appel du context builder original pour récupérer le contexte de base
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        
        // On récupère la classe de l'entité qui est en cours de sérialisation
        $resourceClass = $context['resource_class'] ?? null;

        // Si c'est une entité Pret
        if ($resourceClass === Pret::class) {
            // Si l'utilisateur est un manager
            if ($this->authorizationChecker->isGranted('ROLE_MANAGER')) {
                // On ajoute le groupe get_role_manager qui permet de voir tous les prêts
                $context['groups'][] = 'get_role_manager';
            }
            // Si l'utilisateur est un adhérent
            elseif ($this->authorizationChecker->isGranted('ROLE_ADHERENT')) {
                // On ajoute le groupe get_role_adherent qui permet de voir uniquement ses propres prêts
                $context['groups'][] = 'get_role_adherent';
            }

            // Si l'utilisateur est un admin
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                // On ajoute le groupe get_role_admin qui permet d'avoir accès à toutes les données
                $context['groups'][] = 'get_role_admin';
            }
        }

        return $context;
    }
}
