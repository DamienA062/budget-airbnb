<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//HasLifecycleCallbacks prévient à doctrine qu'il y a des fonctions liées au cycle de vie
//Vich\Uploadable indique que l'entité contient des fichier uploadable
/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255)
     */
    private $title;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(min=30, max=1000)
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, max=255)
     */
    private $content;

    /** 
     * @ORM\Column(type="datetime")
     * 
     * @var \DateTime|null
     */
    private $updatedAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /** 
     * @var File
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="coverImage")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=1, max=10)
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->coverImage = "";
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

    /**
     * Permet la génération du slug
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return (new Slugify())->slugify($this->title);
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

    public function formatPrice()
    {
        return number_format($this->price, 0, '', ' ').' €';
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

    /**
     * @return string|null
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    /**
     * @param string|null $coverImage
     * 
     * @return self
     */
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
     * Get the value of imageFile
     *
     * @return  File|null
     */ 
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set the value of imageFile
     *
     * @param  File|null  $imageFile
     *
     * @return  self
     */ 
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;

        // Only change the updated af if the file is really uploaded to avoid database updates.
        // This is needed when the file should be set when loading the entity.
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }
}
