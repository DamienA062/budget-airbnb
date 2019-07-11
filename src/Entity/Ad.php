<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//HasLifecycleCallbacks prévient à doctrine qu'il y a des fonctions liées au cycle de vie
//Vich\Uploadable indique que l'entité contient des fichier uploadable
/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity("title", message="Titre déjà utilisé")
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
     * @Assert\Length(min=8, max=25)
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
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverImage;

    /** 
     * @var File|null
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Format d'image ivalide, seul le jpg est accepté !")
     * @Vich\UploadableField(mapping="ad_image", fileNameProperty="coverImage")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=1, max=9)
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public $type;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    
    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach($this->comments as $comment)
        {
            if($comment->getAuthor() === $author) return $comment;
        }
        return null;
    }

    /**
     * Calcule la note moyenne d'une annonce en fonction des avis
     *
     * @return float|int
     */
    public function getAvgRatings()
    {
        $sum = 0;
        
        foreach($this->comments as $comment)
        {
            $sum += $comment->getRating(); 
        }

        if(count($this->comments->toArray()) == 0)
        {
            $avgRating = 'Pas encore noté';
        }else{
            $avgRating = $sum / count($this->comments->toArray());
        }
        
        
        if(is_int($avgRating))
        {
            $this->type = 'int';
        }else{
            $this->type = 'float';
        }
        return $avgRating;
    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce
     * Le but est d'avoir les jours précis qui se trouve entre la range de la date de départ et d'arrivée
     * comme on a que la date de départ et la date d'arrivée
     *
     * @return array Un tableau d'objets DateTime représentant les jours d'occupation
     */
    public function getNotAvailableDays()
    {
        $notAvailableDays = [];

        //pour chaque réservation
        foreach($this->bookings as $booking)  
        {
            //Conversion de la date de départ et d'arrivée en timestamp
            $startDateTS = $booking->getStartDate()->getTimestamp();
            $endDateTS = $booking->getEndDate()->getTimestamp();
            $dayTimeInSeconds = 24*60*60;

            //Range renvoit un tableau(3ème param est le step), donc on stock tous les jours en Timestamp de la date d'arrivée à la date de départ
            $daysBetweenInTimestamp = range($startDateTS, $endDateTS, $dayTimeInSeconds);

            //On convertit notre tableau de timestamp ($daysBetweenInTimestamp) en un tableau avec des objet de format DateTime() avec array_map()
            $days = array_map(function($dayTimestamp){
                return new \DateTime(date('Y-m-d', $dayTimestamp));
            }, $daysBetweenInTimestamp);
            
            $notAvailableDays = array_merge($notAvailableDays, $days);
        }

        return $notAvailableDays;
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
    public function setCoverImage(string $coverImage = null): self
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTime|null
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param  \DateTime|null  $updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
