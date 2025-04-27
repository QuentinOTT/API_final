<?php

namespace App\Services;

use App\Entity\Pret;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PretSubscriber implements EventSubscriberInterface
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser',EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult(); //recupere l'entité qui à declenché l'évènement 
        $method = $event->getRequest()->getMethod(); //récupère la méthode invoquée dans la request
        $adherent = $this->token->getToken()->getUser(); // récupere l'utilisateur connecté
        if ($entity instanceof Pret ){
            if($method == Request::METHOD_POST) {                      //s'il s'agit bien d'un opération Post 
                $entity->setAdherent($adherent);             // on écrit l'adhérent dans la propriété adherent de l'entity Pret
            } elseif ($method == Request::METHOD_PUT){
                if($entity->getDateRetourReelle() == null) {
                    $entity->getLivre()->setDispo(false);
                }else{
                    $entity->getLivre()->setDispo(true);
                }
            } elseif ($method == Request::METHOD_DELETE) 
            {
                $entity->getLivre()->setDispo(true);
            }
            return;
        }
    }
}