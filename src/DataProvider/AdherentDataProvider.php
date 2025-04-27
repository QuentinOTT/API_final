<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Symfony\Component\Security\Core\Security;

class AdherentDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $security;
    private $adherentRepository;

    public function __construct(Security $security, AdherentRepository $adherentRepository)
    {
        $this->security = $security;
        $this->adherentRepository = $adherentRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Adherent::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connectu00e9, on retourne une liste vide
        if (!$user) {
            return [];
        }

        // Si l'utilisateur est un manager ou un admin, il peut voir tous les adhu00e9rents
        if ($this->security->isGranted('ROLE_MANAGER')) {
            return $this->adherentRepository->findAll();
        }

        // Pour un adhu00e9rent, il ne peut voir que son propre profil
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Retourner uniquement l'adhu00e9rent connectu00e9
            return [$user];
        }

        return [];
    }
}
