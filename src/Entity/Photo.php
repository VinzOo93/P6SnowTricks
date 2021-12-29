<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="photos", cascade={"persist"})
     */
    private $trick;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $folderId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param  $file
     */
    public function setFile( $file): void
    {
        $this->file = $file;
    }

    public function getFolderId(): ?string
    {
        return $this->folderId;
    }

    public function setFolderId(string $folderId): self
    {
        $this->folderId = $folderId;

        return $this;
    }

    public function __toString()
    {
        return $this->getSlug() ;
    }

}
