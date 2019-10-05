<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Helper\TrackHashHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddAlbumService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AddArtistService
     */
    private $addArtistService;

    public function __construct(EntityManagerInterface $entityManager, AddArtistService $addArtistService)
    {
        $this->entityManager = $entityManager;
        $this->addArtistService = $addArtistService;
    }

    public function execute(\stdClass $json, SymfonyStyle $io): void
    {
        foreach ($json->items as $album){
            $check = $this->entityManager->getConnection()->executeQuery(
                'SELECT id FROM album WHERE id = ?', [$album->id], [\PDO::PARAM_STR]
            )->fetchColumn();
            if($check){
                continue;
            }
            $this->entityManager->persist(
                new Album($album->id, $album->name, $album->album_type, TrackHashHelper::getArtistNames($album))
            );
            $this->entityManager->flush();
            $io->writeln(
                sprintf('Create new %s "%s"', $album->album_type, $album->name)
            );
            foreach ($album->artists as $artist){
                $this->addArtistService->execute($artist, $io);
                $this->entityManager->getConnection()->executeQuery(
                    'INSERT INTO album2artist (album_id, artist_id) VALUES (?,?)',
                    [$album->id, $artist->id],
                    [\PDO::PARAM_STR, \PDO::PARAM_STR]
                );
            }
        }
    }
}

