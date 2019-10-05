<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="add_new_track")
 * @ORM\Entity()
 */
final class AddNewTrack
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
     * @var integer
     * @ORM\Column(name="popularity", type="integer")
     */
    private $popularity;

    /**
     * @var float
     * @ORM\Column(name="duration", type="float")
     */
    private $duration;

    public function __construct(string $id, string $name, string $artistName, int $popularity, float $duration)
    {
        $this->id = $id;
        $this->name = $name;
        $this->artistName = $artistName;
        $this->createdAt = new \DateTime();
        $this->popularity = $popularity;
        $this->duration = $duration;
    }
}
