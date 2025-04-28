<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Repository\AdherentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatsController extends AbstractController
{
    /**
     * @Route(
     *      path="/apiplatform/adherent/nbPretsParAdherent",
     *      name="adherent_nbPrets",
     *      methods={"GET"}
     * )
     */
    public function nombrePretsParAdherent(AdherentRepository $repo)
    {
        $nbPretsParAdherent = $repo->nbPretsParAdherent();
        return $this->json($nbPretsParAdherent);
    }

    /**
     * @Route(
     *      path="/apiplatform/livres/meilleurslivres",
     *      name="adherent_meilleurslivres",
     *      methods={"GET"}
     * )
     */
    public function meilleursLivres(LivreRepository $repo)
    {
        $meilleursLivres = $repo->TrouveMeilleursLivres();
        return $this->json($meilleursLivres);
    }
}
