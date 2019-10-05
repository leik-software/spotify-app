<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="track2playlist")
 * @ORM\Entity()
 */
final class Track2Playlist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Track
     * @ORM\ManyToOne(targetEntity="Track")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @var Playlist
     * @ORM\ManyToOne(targetEntity="Playlist")
     * @ORM\JoinColumn(name="playlist_id", referencedColumnName="id")
     */
    protected $playlist;

    /**
     * @var \DateTime
     * @ORM\Column(name="added_at", type="datetime", nullable=false)
     */
    protected $addedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var integer
     * @ORM\Column(name="track_number", type="integer")
     */
    protected $trackNumber = 0;


}
