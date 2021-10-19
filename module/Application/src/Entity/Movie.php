<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="movies")
 */
class Movie
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Language", inversedBy="movies", cascade={"all"})
     */
    private $languages;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies", cascade={"all"})
     */
    private $genres;

    public function __construct()
    {
        $this->languages = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLanguages(): array
    {
        return $this->languages->toArray();
    }

    public function addLanguage(Language $language): void
    {
        if ($this->languages->contains($language)) {
            return;
        }

        $this->languages->add($language);
        $language->addMovie($this);
    }

    public function getGenres(): array
    {
        return $this->genres->toArray();
    }

    public function addGenre(Genre $genre): void
    {
        if ($this->genres->contains($genre)) {
            return;
        }

        $this->genres->add($genre);
        $genre->addMovie($this);
    }
}
