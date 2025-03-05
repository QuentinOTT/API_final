<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/listeRegions", name="listeRegions")
     */
    public function listeRegions(SerializerInterface $serializer): Response
    {
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions'); 
        // $mesRegionsTab = $serializer->decode($mesRegions, 'json');
        // $mesRegionsObjet = $serializer->denormalize($mesRegionsTab, 'App\Entity\Region[]');
        $mesRegions = $serializer->deserialize($mesRegions,'App\Entity\Region[]','json');
        // dump($mesRegionsTab);
        // die(); 
        return $this->render('api/index.html.twig', [
            'mesRegions' => $mesRegions
        ]);
    }

    /**
     * @Route("/listeDepsParRegion", name="listeDepsParRegion")
     */
    public function listeDepsParRegion(Request $request, SerializerInterface $serializer): Response
    {
        $codeRegion=$request->query->get('region');
        // je récupère des régions 
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions'); 
        $mesRegions = $serializer->deserialize($mesRegions,'App\Entity\Region[]','json'); 

        if($codeRegion == null || $codeRegion == "toutes"){
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/departements');   
        }else{
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
        }
    
            
        $mesDeps = $serializer->decode($mesDeps,'json');

      
        return $this->render('api/listDepsParRegion.html.twig', [
            'mesDeps' => $mesDeps,
            'mesRegions' => $mesRegions
        ]);
    }
}
