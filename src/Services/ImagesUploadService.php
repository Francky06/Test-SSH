<?php

namespace App\Services;

use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImagesUploadService extends AbstractController {

	public function upload($ad,$manager) {

		 foreach($ad->file as $file) {
                //enleve tout ce qu il y a apres le .  
                //$position_point = strpos($file->getClientOriginalName(),'.');
                //garde tout entre la position 0 et le point
                //$orignalName = substr($file->getClientOriginalName(),0,$position_point);
                //$orignalName = preg_replace('#\.(jpg|png|gif)*$#','', $file->getClientOriginalName());

                $orignalName = preg_replace('#\.[a-zA-Z-0-9]*$#','', $file->getClientOriginalName());
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $upload = new ImageUpload();
                $upload->setAd($ad)
                		->setName($orignalName)
                		->setUrl('/uploads/'.$fileName);

                $manager->persist($upload);
                $file->move($this->getParameter('images_directory'), $fileName);
                
            }
	}
}