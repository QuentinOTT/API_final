<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Repository\PretRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class PretController extends AbstractController
{
    private $pretRepository;
    private $security;

    public function __construct(PretRepository $pretRepository, Security $security)
    {
        $this->pretRepository = $pretRepository;
        $this->security = $security;
    }

    public function getPret(Request $request, int $id): Pret
    {
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connecté, accès refusé
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour accéder à cette ressource");
        }

        // Récupérer le prêt
        $pret = $this->pretRepository->find($id);
        
        // Si le prêt n'existe pas, retourner une 404
        if (!$pret) {
            throw $this->createNotFoundException("Le prêt demandé n'existe pas");
        }

        // Si l'utilisateur est un manager ou un admin, il peut voir tous les prêts
        if ($this->security->isGranted('ROLE_MANAGER')) {
            return $pret;
        }

        // Pour un adhérent, vérifier si le prêt lui appartient
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Si le prêt appartient à l'adhérent connecté
            // L'utilisateur connecté est une instance de l'entité Adherent
            if ($pret->getAdherent() && $pret->getAdherent() === $user) {
                return $pret;
            } else {
                // Sinon, accès refusé avec message personnalisé
                throw $this->createAccessDeniedException("Ce prêt n'est pas le vôtre");
            }
        }

        // Par défaut, accès refusé
        throw $this->createAccessDeniedException("Vous n'avez pas les droits nécessaires");
    }
}
