<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 * @ORM\Table(name="track")
 */
final class Track
{
    /**
     * @var string
     * @ORM\Column(name="id", type="string", length=55, nullable=false, unique=true)
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
     * @ORM\Column(name="artist_name", type="string", length=255)
     */
    protected $artistName = '';

    /**
     * @var string
     * @ORM\Column(name="track_hash", type="string", length=255)
     */
    protected $trackHash = '';

    /**
     * @var boolean
     * @ORM\Column(name="saved", type="boolean")
     */
    protected $saved = false;

    /**
     * @var integer
     * @ORM\Column(name="popularity", type="integer")
     */
    private $popularity;

    /**
     * @var float
     * @ORM\Column(name="duration", type="float")
     */
    private $duration;

    public function __construct(\stdClass $json, string $trackHash, string $artistName)
    {
        $this->id = $json->id;
        $this->name = $json->name;
        $this->trackHash = $trackHash;
        $this->artistName = $artistName;
        $this->createdAt = new \DateTime();
        $this->popularity = $json->popularity ?? 0;
        $this->duration = $json->duration_ms/1000/60;
    }

}
