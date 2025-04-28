<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdherentController extends AbstractController
{
    /**
     * renvoie le nombre de prêts pour un adhérent
     * @Route(
     *     path="apiplatform/adherent/{id}/prets/count",
     *     name="adherent_prets_count",
     *     methods={"GET"},
     *     defaults={
     *         "_controller"="App\\Controller\\AdherentController::nombrePrets",
     *         "_api_ressource_class"="App\\Entity\\Adherent",
     *         "_api_item_operation_name"="getNbPrets"
     *     }
     * )
     */
    
    public function nombrePrets(Adherent $data)
    {
        $count = $data->getPrets()->count();
        return $this->json([
            "id" => $data->getId(),
            "nombre_de_prets" => $count,
        ]);
    }

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
        
        // Si l'utilisateur n'est pas connecté, accès refusé
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour accéder à cette ressource");
        }

        // Récupérer l'adhérent demandé
        $adherent = $this->adherentRepository->find($id);
        
        // Si l'adhérent n'existe pas, retourner une 404
        if (!$adherent) {
            throw $this->createNotFoundException("L'adhérent demandé n'existe pas");
        }

        // Si l'utilisateur est un manager ou un admin, il peut voir tous les adhérents
        if ($this->security->isGranted('ROLE_MANAGER')) {
            return $adherent;
        }

        // Pour un adhérent, vérifier s'il consulte son propre profil
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Si l'adhérent consulte son propre profil
            if ($adherent === $user) {
                return $adherent;
            } else {
                // Sinon, accès refusé avec message personnalisé
                throw $this->createAccessDeniedException("Vous ne pouvez consulter que votre propre profil");
            }
        }

        // Par défaut, accès refusé
        throw $this->createAccessDeniedException("Vous n'avez pas les droits nécessaires");
    }

    public function updateAdherent(Request $request, int $id, Adherent $data): Adherent
    {
        $user = $this->security->getUser();
        
        // Si l'utilisateur n'est pas connecté, accès refusé
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour accéder à cette ressource");
        }

        // Récupérer l'adhérent à modifier
        $adherent = $this->adherentRepository->find($id);
        
        // Si l'adhérent n'existe pas, retourner une 404
        if (!$adherent) {
            throw $this->createNotFoundException("L'adhérent demandé n'existe pas");
        }

        // Si l'utilisateur est un manager, il peut modifier les adhérents mais pas leurs rôles, mails et mots de passe
        if ($this->security->isGranted('ROLE_MANAGER') && !$this->security->isGranted('ROLE_ADMIN')) {
            // Empêcher la modification des rôles, du mail et du mot de passe
            if ($request->getContent()) {
                $content = json_decode($request->getContent(), true);
                if (isset($content['roles']) || isset($content['mail']) || isset($content['password'])) {
                    throw $this->createAccessDeniedException("Les managers ne peuvent pas modifier les rôles, les mails ou les mots de passe des adhérents");
                }
            }
            return $adherent;
        }
        
        // Si l'utilisateur est un admin, il peut tout modifier
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $adherent;
        }

        // Pour un adhérent, vérifier s'il modifie son propre profil
        if ($this->security->isGranted('ROLE_ADHERENT')) {
            // Si l'adhérent modifie son propre profil
            if ($adherent === $user) {
                // Empêcher la modification des rôles et du mot de passe
                if ($request->getContent()) {
                    $content = json_decode($request->getContent(), true);
                    if (isset($content['roles']) || isset($content['password'])) {
                        throw $this->createAccessDeniedException("Vous ne pouvez pas modifier vos rôles ou votre mot de passe");
                    }
                }
                return $adherent;
            } else {
                // Sinon, accès refusé avec message personnalisé
                throw $this->createAccessDeniedException("Vous ne pouvez modifier que votre propre profil");
            }
        }

        // Par défaut, accès refusé
        throw $this->createAccessDeniedException("Vous n'avez pas les droits nécessaires");
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
