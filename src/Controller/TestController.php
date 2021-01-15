<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
    	$a = 'Francky';
    	$tab = ['eric'=>52, 'gerald'=>53,'flo'=>26];
    	dump($tab);
    	dump($this);

     
        return $this->render('test/test.html.twig', [
        	'prenom'=>$a,
        	'tableau'=>$tab,
        	'age'=>36
        	 ]);
    }
}
