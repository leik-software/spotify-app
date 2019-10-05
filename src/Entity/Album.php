<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="album")
 * @ORM\Entity()
 */
final class Album
{
    /**
     * @var string
     * @ORM\Column(name="id", type="string", length=55, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id = '';

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name = '';

    /**
     * @var string
     * @ORM\Column(name="album_type", type="string", length=255)
     */
    protected $albumType = '';

    /**
     * @var string
     * @ORM\Column(name="artist", type="string", length=255)
     */
    protected $artist = '';

    /**
     * @var boolean
     * @ORM\Column(name="scanned", type="boolean")
     */
    protected $scanned = false;

    public function __construct(string $id, string $name, string $albumType, string $artist)
    {
        $this->id = $id;
        $this->name = $name;
        $this->albumType = $albumType;
        $this->createdAt = new \DateTime();
        $this->artist = $artist;
    }
}
