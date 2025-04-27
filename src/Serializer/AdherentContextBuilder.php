<?php

namespace App\Serializer;

use App\Entity\Adherent;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Context builder pour l'entité Adherent qui gère la visibilité des données selon le rôle de l'utilisateur.
 */
final class AdherentContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;
    private $security;

    /**
     * Constructeur qui reçoit le decorateur, le checker d'autorisation et le service de sécurité.
     */
    public function __construct(
        SerializerContextBuilderInterface $decorated, 
        AuthorizationCheckerInterface $authorizationChecker,
        Security $security
    ) {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;
    }

    /**
     * Méthode principale qui modifie le contexte de sérialisation selon le rôle de l'utilisateur.
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        // Appel du context builder original pour récupérer le contexte de base
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        
        // On récupère la classe de l'entité qui est en cours de sérialisation
        $resourceClass = $context['resource_class'] ?? null;

        // Si c'est une entité Adherent
        if ($resourceClass === Adherent::class && $normalization === true) {
            $user = $this->security->getUser();
            $adherent = null;
            
            // Si on est en train de sérialiser un item (un adhérent spécifique)
            if (isset($context['item_operation_name']) && $context['item_operation_name'] === 'get') {
                // On récupère l'ID de l'adhérent demandé depuis l'attribut de la requête
                $id = $request->attributes->get('id');
                
                // Si l'utilisateur est un adhérent et qu'il consulte son propre profil
                if ($user instanceof Adherent && $user->getId() == $id) {
                    // On ajoute le groupe get_role_adherent qui permet de voir ses propres données
                    $context['groups'][] = 'get_role_adherent';
                }
            }
            
            // Si l'utilisateur est un manager
            if ($this->authorizationChecker->isGranted('ROLE_MANAGER')) {
                // On ajoute le groupe get_role_manager qui permet de voir plus de données
                $context['groups'][] = 'get_role_manager';
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
