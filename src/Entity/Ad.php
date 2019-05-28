<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Une autre annonces possède déjà ce titre, merci de le modifier"
 * )
 */
class Ad{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Le titre doit faire plus de  {{ limit }} characters",
     *      maxMessage = "Le titre doit faire moins de {{ limit }} characters"
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
     * @Assert\Length(
     *      min = 20,
     *      minMessage = "L'introduction doit faire plus de  {{ limit }} characters"
     * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * * @Assert\Length(
     *      min = 100,
     *      minMessage = "La dscription doit faire plus de  {{ limit }} characters"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
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

    public function __construct(){
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Permet d'initializer le slug!
     * @ORM\PrePersist
     * @ORM\PostUpdate
     *
     * @return void
     */
    public function initializeSlug(){
        if (empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     * @param User $author
     * @return Comment|null
     */
    public function getCommentfromAuthor(User $author){
        foreach ($this->comments as $comment){
            if ($comment->getAuthor() === $author) return $comment;
        }

        return null;
    }

    /**
     * Permet d'obtenir la moyenne gllobale des notes pour cette annonce
     * @return float
     */
    public function getAvgRatings(){
        //calculer la somme des notations
        $sum = array_reduce($this->comments->toArray(), function($total, $comment){
            return $total + $comment->getRating();
        }, 0);
        //Faire la division pour avoir la moyenne
        if (count($this->comments) > 0) return $sum / count($this->comments);
        return 0;
    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cet annonce
     *
     * @return array
     */
    public function getNotAvailableDays(){
        $notAvailableDays = [];

        foreach($this->bookings as $booking){
            //calculer les jours qui se trouvent entre la date d'arrivée et de départ
            $resultat = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24 * 60 * 60
            );

            $days = array_map(function($dayTimestamp){
                return new \DateTime(date('Y-m-d', $dayTimestamp));
            }, $resultat);

            $notAvailableDays = array_merge($notAvailableDays, $days);
        }

        return $notAvailableDays;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getTitle(): ?string{
        return $this->title;
    }

    public function setTitle(string $title): self{
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string{
        return $this->slug;
    }

    public function setSlug(string $slug): self{
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float{
        return $this->price;
    }

    public function setPrice(float $price): self{
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string{
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self{
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string{
        return $this->content;
    }

    public function setContent(string $content): self{
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string{
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self{
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int{
        return $this->rooms;
    }

    public function setRooms(int $rooms): self{
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection{
        return $this->images;
    }

    public function addImage(Image $image): self{
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self{
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User{
        return $this->author;
    }

    public function setAuthor(?User $author): self{
        $this->author = $author;

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
