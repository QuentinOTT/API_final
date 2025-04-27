<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Pret;
use App\Repository\PretRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class PretDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $security;
    private $pretRepository;

    public function __construct(Security $security, PretRepository $pretRepository)
    {
        $this->security = $security;
        $this->pretRepository = $pretRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Pret::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }

        if ($this->security->isGranted('ROLE_MANAGER')) {
            return $this->pretRepository->findAll();
        }

        return $this->pretRepository->findBy(['adherent' => $user]);
    }
    

}
