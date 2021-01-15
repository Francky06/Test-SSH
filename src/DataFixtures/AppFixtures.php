<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {


   //injection dependance de l encodeur dans la fixture
        private $passwordEncoder;
        
        public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
             $this->passwordEncoder = $passwordEncoder;
         }



    public function load(ObjectManager $manager) {
        // $product = new Product();
        // $manager->persist($product);

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        $adminUser = new User();
        $adminUser->setFirstName("Francky")
                    ->setLastName("Styli")
                    ->setEmail("franckycoding@gmail.com") 
                    ->setPicture("https://via.placeholder.com/64")
                    ->setIntroduction("God")  
                    ->setDescription("Deus") 
                    ->setSlug("francky-styli")
                    ->setHash($this->passwordEncoder->encodePassword(
                            $adminUser,
                            'password'
                             ))
                    ->addUserRole($adminRole);
                    $manager->persist($adminUser);
                  
        //user
        for ($l=1; $l <5 ; $l++) { 
                $user= new User();
                $user->setFirstName("prenom$l")
                    ->setLastName("nom$l")
                    ->setEmail("test$l@test.fr") 
                    ->setPicture("https://via.placeholder.com/64")
                    ->setIntroduction("introduction$l")  
                    ->setDescription("description$l") 
                    ->setSlug("prenom$l-nom$l");

                    $user->setHash($this->passwordEncoder->encodePassword(
                                 $user,
                                 'password'
                             ));

                    $manager->persist($user);
                    $manager->flush();  
                    //on ajoute l id pour eviter les doublons
                    $slug2 = $user->getSlug().'_'.$user->getId();
                    $user->setSlug($slug2);
                    $manager->persist($user);
                   

        //anonces
        for ($i=0; $i < mt_rand(1,4) ; $i++) { 
			$slugify = new Slugify();
			// $slugify->slugify('Titre de l'annonce n°: $i');
			$title = "Titre de l'annonce n°: $i";
			$slug = $slugify->slugify($title);
        	$ad = new Ad();
        	$ad->setTitle("Titre de l'annonce n°: $i")
        		->setSlug($slug)
        		->setPrice(mt_rand(40,200))
        		->setIntroduction("Introduction de <strong>l'annonce n°: $i</strong>")
        		->setContent("Contenu de <strong>l'annonce n°: $i</strong>")
        		->setRooms(mt_rand(1,5))
        		->setCoverImage("https://via.placeholder.com/350")
                ->setAuthor($user);

         for ($j=0; $j < mt_rand(1,5); $j++) { 
                    $image = new Image();
                    $image->setUrl("https://via.placeholder.com/350");
                    $image->setCaption("<p>Contenu de l'annonce n°: $j</p>");
                    $image->setAd($ad);
                    $manager->persist($image);       
                }
                //fait persister
        		$manager->persist($ad);
        		//ecrit ds la bdd
        		$manager->flush();
        		//dump($ad->getId());
        		//new slug
        		$slug2 = $ad->getSlug().'_'.$ad->getId();
        		$ad->setSlug($slug2);
        		$manager->persist($ad);
        		$manager->flush();
        		


        }

        }
    }
}
