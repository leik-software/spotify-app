<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="artist")
 * @ORM\Entity()
 */
final class Artist
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
     * @var boolean
     * @ORM\Column(name="follow", type="boolean")
     */
    protected $follow = false;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new \DateTime();
    }
}
