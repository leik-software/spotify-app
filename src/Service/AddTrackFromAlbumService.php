<?php declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddTrackFromAlbumService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AddTrackService
     */
    private $addTrackService;

    public function __construct(EntityManagerInterface $entityManager, AddTrackService $addTrackService)
    {
        $this->entityManager = $entityManager;
        $this->addTrackService = $addTrackService;
    }

    public function execute(string $albumId, \stdClass $track, SymfonyStyle $io): void
    {
        if(!$track->id){
            $io->writeln(sprintf('track %s has no id', $track->name));
            return;
        }
        $this->addTrackService->execute($track, $io);
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT track_id FROM track2album WHERE track_id = ? AND album_id = ?',
            [$track->id, $albumId], [\PDO::PARAM_STR, \PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return;
        }
        $this->entityManager->getConnection()->executeQuery(
            'INSERT INTO track2album (track_id, album_id, created_at, track_number) VALUES (?,?,now(),?)',
            [
                $track->id,
                $albumId,
                $track->track_number
            ],
            [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT
            ]
        );
    }
}
