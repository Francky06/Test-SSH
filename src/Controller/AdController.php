<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\ImageUpload;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {	
    	//cherchez repo associe a Ad
    	//$repo = $this->getDoctrine()->getRepository(Ad::class);
    	//prends tt les enregistrements
    	$ads=$repo->findAll();
    	
        return $this->render('ad/index.html.twig', [
        	//transmission a twig de la cle ads contentant $ads
        	'ads'=>$ads,
            
        ]);
    }


    /**
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_ADMIN")
    */
    public function create (EntityManagerInterface $manager, Request $request, ImagesUploadService $upload)
    {   
       $ad = new Ad();

       $ad->setAuthor($this->getUser());
      /* enleve images du prototype
        $image = new Image();
        $image->setUrl("http")
                ->setCaption("légende de l'image 1");
                $ad->addImage($image);
        $image2 = new Image();
        $image2->setUrl("http")
                ->setCaption("légende de l'image 1");
                $ad->addImage($image2);
      */ 
        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $slugify = new Slugify();
            $slug = $slugify->Slugify($ad->getTitle());
            $ad->setSlug($slug);

           //img avec lien
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            /* inclusion de la fonction 
               gestion des images uploadees
            */
            $upload->upload($ad,$manager);
            $manager->persist($ad);
            $manager->flush();
       
                $slug2 = $ad->getSlug().'_'.$ad->getId();
                $ad->setSlug($slug2);
                $manager->persist($ad);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\' annonce de titre'.$ad->getTitle().' a bien été enregistrée'
                );

                return $this->redirectToRoute('ads_show', ['slug'=>$ad->getSlug()]);
          }  
        return $this->render('ad/new.html.twig', [
            'form'=>$form->createView(),                          
        ]);
    }

     /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message = "Cette annonce ne vous appartient pas !")
    */
    public function edit (EntityManagerInterface $manager, Request $request, Ad $ad, ImagesUploadService $upload)
    {   
        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $slugify = new Slugify();
            $slug = $slugify->Slugify($ad->getTitle());
            $ad->setSlug($slug);

            //gestion image up
            $upload->upload($ad,$manager);
            //supression image
            $tabid = $ad->tableau_id;
            //enleve la premiere virgule
            $tabid = preg_replace('#^,#','',$tabid);
            // transforme string en tab
            $tabid = explode(',', $tabid);

            foreach ($tabid as $id) {
                foreach ($ad->getImageUploads() as $image) {
                    if($id == $image->getid()){
                        $manager->remove($image);
                        $manager->flush();
                        //chemin absolu
                        unlink($_SERVER['DOCUMENT_ROOT'].$image->getUrl());
                    }
    
            }
            }
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
           
            }
            $manager->persist($ad);
            $manager->flush();
       
            $slug2 = $ad->getSlug().'_'.$ad->getId();
                $ad->setSlug($slug2);
                $manager->persist($ad);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\' annonce de titre'.$ad->getTitle().' a bien été modifiée'
                );

                return $this->redirectToRoute('ads_show', ['slug'=>$ad->getSlug()]);
          }  

        return $this->render('ad/edit.html.twig', [
            'form'=>$form->createView(),
            'ad'=>$ad,
            
            
            
        ]);
    }


//PARAM CONVERTER met variable(propriete) dans la route, et essayer de concorder avec une entite AD.
    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show (Ad $ad)
    {	
    	//$ad=$repo->findOneBySlug($slug);
    	//dump($annonces);
        return $this->render('ad/show.html.twig', [
        	'ad'=>$ad,
        	
            
        ]);
    }


    /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message = "Cette annonce ne vous appartient pas !")
     */
    public function delete (EntityManagerInterface $manager, Ad $ad) {
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash(
                    'success',
                    'L\' annonce a bien été supprimée'

    );
         return $this->redirectToRoute('ads_index');
    }

    }


