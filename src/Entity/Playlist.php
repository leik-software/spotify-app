<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 * @ORM\Table(name="playlist")
 */
final class Playlist
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
     * @var boolean
     * @ORM\Column(name="autoscan", type="boolean")
     */
    protected $autoscan = false;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_scan", type="datetime", nullable=true)
     */
    protected $lastScan;

    public function __construct(\stdClass $json)
    {
        $this->id = $json->id;
        $this->name = $json->name;
        $this->autoscan = true;
        $this->createdAt = new \DateTime();
    }

    public function scanned(): void{
        $this->lastScan = new \DateTime();
    }
}
