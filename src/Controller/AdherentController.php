<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AdherentController extends AbstractController
{
    private $adherentRepository;
    private $security;
    private const DEFAULT_ROLE = 'ROLE_ADHERENT';

    public function __construct(AdherentRepository $adherentRepository, Security $security)
    {
        $this->adherentRepository = $adherentRepository;
        $this->security = $security;
    }

    public function getAdherent(Request $request, int $id): Adherent
    {
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connectu00e9, accu00e8s refusu00e9
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez u00eatre connectu00e9 pour accu00e9der u00e0 cette ressource");
        }

        // Ru00e9cupu00e9rer l'adhu00e9rent demandu00e9
        $adherent = $this->adherentRepository->find($id);
        
        // Si l'adhu00e9rent n'existe pas, retourner une 404
        if (!$adherent) {
            throw $this->createNotFoundException("L'adhu00e9rent demandu00e9 n'existe pas");
        }

        // Si l'utilisateur est un manager ou un admin, il peut voir tous les adhu00e9rents
        if ($this->security->isGranted('ROLE_MANAGER')) {
            return $adherent;
        }

        // Pour un adhu00e9rent, vu00e9rifier s'il consulte son propre profil
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Si l'adhu00e9rent consulte son propre profil
            if ($adherent === $user) {
                return $adherent;
            } else {
                // Sinon, accu00e8s refusu00e9 avec message personnalisu00e9
                throw $this->createAccessDeniedException("Vous ne pouvez consulter que votre propre profil");
            }
        }

        // Par du00e9faut, accu00e8s refusu00e9
        throw $this->createAccessDeniedException("Vous n'avez pas les droits nu00e9cessaires");
    }

    public function updateAdherent(Request $request, int $id, Adherent $data): Adherent
    {
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connectu00e9, accu00e8s refusu00e9
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez u00eatre connectu00e9 pour accu00e9der u00e0 cette ressource");
        }

        // Ru00e9cupu00e9rer l'adhu00e9rent u00e0 modifier
        $adherent = $this->adherentRepository->find($id);
        
        // Si l'adhu00e9rent n'existe pas, retourner une 404
        if (!$adherent) {
            throw $this->createNotFoundException("L'adhu00e9rent demandu00e9 n'existe pas");
        }

        // Si l'utilisateur est un manager, il peut modifier les adhu00e9rents mais pas leurs ru00f4les, mails et mots de passe
        if ($this->security->isGranted('ROLE_MANAGER') && !$this->security->isGranted('ROLE_ADMIN')) {
            // Empu00eacher la modification des ru00f4les, du mail et du mot de passe
            if ($request->getContent()) {
                $content = json_decode($request->getContent(), true);
                if (isset($content['roles']) || isset($content['mail']) || isset($content['password'])) {
                    throw $this->createAccessDeniedException("Les managers ne peuvent pas modifier les ru00f4les, les mails ou les mots de passe des adhu00e9rents");
                }
            }
            return $adherent;
        }
        
        // Si l'utilisateur est un admin, il peut tout modifier
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $adherent;
        }

        // Pour un adhu00e9rent, vu00e9rifier s'il modifie son propre profil
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Si l'adhu00e9rent modifie son propre profil
            if ($adherent === $user) {
                // Empu00eacher la modification des ru00f4les et du mot de passe
                if ($request->getContent()) {
                    $content = json_decode($request->getContent(), true);
                    if (isset($content['roles']) || isset($content['password'])) {
                        throw $this->createAccessDeniedException("Vous ne pouvez pas modifier vos ru00f4les ou votre mot de passe");
                    }
                }
                return $adherent;
            } else {
                // Sinon, accu00e8s refusu00e9 avec message personnalisu00e9
                throw $this->createAccessDeniedException("Vous ne pouvez modifier que votre propre profil");
            }
        }

        // Par du00e9faut, accu00e8s refusu00e9
        throw $this->createAccessDeniedException("Vous n'avez pas les droits nu00e9cessaires");
    }
    
    public function createAdherent(Request $request, Adherent $data): Adherent
    {
        // Vérifier si l'utilisateur est un manager
        if (!$this->security->isGranted('ROLE_MANAGER')) {
            throw $this->createAccessDeniedException("Seuls les managers peuvent créer des adhérents");
        }
        
        // S'assurer que le rôle par défaut est bien ROLE_ADHERENT
        // Cette vérification est redondante car le constructeur de l'entité Adherent définit déjà le rôle par défaut
        // mais c'est une sécurité supplémentaire
        if ($request->getContent()) {
            $content = json_decode($request->getContent(), true);
            if (isset($content['roles'])) {
                throw $this->createAccessDeniedException("Les managers ne peuvent pas définir les rôles lors de la création d'un adhérent");
            }
        }
        
        // S'assurer que le rôle est bien ROLE_ADHERENT
        $data->setRoles([self::DEFAULT_ROLE]);
        
        return $data;
    }
}
