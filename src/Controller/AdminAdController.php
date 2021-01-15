<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index", requirements={"page"="[0-9]{1,}"})
     */
    public function index(AdRepository $repo, $page=1)
    {
    	//limit de 5 enregistrements par page
    	$limit = 3;
    	$start = $limit * $page - $limit; //calcul de l offset
    	$total = count($repo->findAll()); // nb total d annonce
    	$pages = ceil($total/$limit); //arrondi entier superieur



    	//$ads=$repo->findAll();


        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findBy([], [], $limit, $start ),
            'page'=>$page,
            'pages'=>$pages
        ]);
    }
}
