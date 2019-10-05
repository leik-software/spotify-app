<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Track;
use App\Helper\TrackHashHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddTrackService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AddArtistService
     */
    private $addArtistService;
    /**
     * @var AddNewTrackService
     */
    private $addNewTrackService;

    public function __construct(
        EntityManagerInterface $entityManager,
        AddArtistService $addArtistService,
        AddNewTrackService $addNewTrackService
    )
    {
        $this->entityManager = $entityManager;
        $this->addArtistService = $addArtistService;
        $this->addNewTrackService = $addNewTrackService;
    }

    public function execute(\stdClass $track, SymfonyStyle $io, $force = false): bool
    {
        $this->addNewTrackService->execute($track, $io, $force);
        $trackHash = TrackHashHelper::generateHash($track);
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT id FROM track WHERE id = ? ', [$track->id],[\PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return false;
        }
        $this->entityManager->persist(
            new Track($track, $trackHash, TrackHashHelper::getArtistNames($track))
        );
        $io->writeln(
            sprintf(
                'Track "%s" added', $track->name
            )
        );

        $this->entityManager->flush();
        foreach ($track->artists as $artist){
            $this->addArtistService->execute($artist, $io);
            $this->entityManager->getConnection()->executeQuery(
                'INSERT INTO track2artist (track_id, artist_id) VALUES (?,?)',
                [$track->id, $artist->id],
                [\PDO::PARAM_STR, \PDO::PARAM_STR]
            );
        }
        return true;
    }
}
