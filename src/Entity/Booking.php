<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Mauvais format de date")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être ultérieure à la date d'aujourd'hui !")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Mauvais format de date")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être supérieure à la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Callback appelé à chaque fois qu'on crée une réservation
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist()
    {
        if(empty($this->createdAt))
        {
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount))
        {
            //prix de l'annonce * nombre de jour
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookableDates()
    {
        //On récupère les dates déjà reservées
        $notAvailableDays = $this->ad->getNotAvailableDays();

        //On récupère les dates de ma réservation
        $bookingDays = $this->getDays();

        $formatDay = function($day){
            return $day->format('Y-m-d');
        };

        //On convertit nos tableaux qui sont des objets DateTime en string pour que la comparaison des chaines soient simples
        $notAvailable = array_map($formatDay, $notAvailableDays);

        $days = array_map($formatDay, $bookingDays);


        foreach($days as $day)
        {
            //si un des jour qu'on choisit se trouve dans un jour déjà réservé alors on renvoie false
            if(array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTime représentant les jours de la réservation
     */
    public function getDays()
    {
        //Conversion de la date de départ et d'arrivée en timestamp
        $startDateTS = $this->startDate->getTimestamp();
        $endDateTS = $this->endDate->getTimestamp();
        $dayTimeInSeconds = 24*60*60;

        //Range renvoit un tableau(3ème param est le step), donc on stock tous les jours en Timestamp de la date d'arrivée à la date de départ
        $daysBetweenInTimestamp = range($startDateTS, $endDateTS, $dayTimeInSeconds);

        //On convertit notre tableau de timestamp ($daysBetweenInTimestamp) en un tableau avec des objet de format DateTime() avec array_map()
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $daysBetweenInTimestamp);
        
        //Je retourn les jours qui correspondent à ma réservation
        return $days;
    }

    /**
     * Calcule le nombre de jour d'une réservation
     *
     * @return int
     */
    public function getDuration()
    {
        //La méthode diff fait la différence entre 2 dates et renvoit un objet DateInterval
        //days nous renvoit le nombre de jour
        return ($this->endDate->diff($this->startDate))->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
