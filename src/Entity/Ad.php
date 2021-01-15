<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * @ORM\HasLifecycleCallbacks
 */

class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le titre doit faire un minimum de {{ limit }} caracteres",
     *      maxMessage = "Le titre doit faire un maximum de {{ limit }} caracteres",
     *      allowEmptyString = false
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     *@Assert\Url
     *@Assert\Regex(
     *     pattern="#\.(jpg|png|gif)$#",
     *     match=true,
     *     message="L' url doit se terminer en jpg, png ou gif"
     * )
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true)
     *@Assert\Valid
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=ImageUpload::class, mappedBy="ad", orphanRemoval=true)
     */
    private $imageUploads;


    /**
    *@Assert\All({
    *@Assert\File(         
    *   maxSize="1024k",
    *   mimeTypes= {"image/jpeg", "image/png"},
    *   mimeTypesMessage = "Entrez une image jpg, jpeg, png"
    *)
    *})
    */

    public $file;


 
    public $tableau_id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;
    
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->imageUploads = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImageUpload[]
     */
    public function getImageUploads(): Collection
    {
        return $this->imageUploads;
    }

    public function addImageUpload(ImageUpload $imageUpload): self
    {
        if (!$this->imageUploads->contains($imageUpload)) {
            $this->imageUploads[] = $imageUpload;
            $imageUpload->setAd($this);
        }

        return $this;
    }

    public function removeImageUpload(ImageUpload $imageUpload): self
    {
        if ($this->imageUploads->contains($imageUpload)) {
            $this->imageUploads->removeElement($imageUpload);
            // set the owning side to null (unless already changed)
            if ($imageUpload->getAd() === $this) {
                $imageUpload->setAd(null);
            }
        }

        return $this;
    }


    /**
    * permet de supprimer les imgs dans le dossier uploads
    * @ORM\PostRemove
    */
    public function deleteUploadFiles()
    {
        foreach ($this->getImageUploads() as $image) {                          
                unlink($_SERVER['DOCUMENT_ROOT'] . $image->getUrl());
                
    }
}

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

  
}
