<?php declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddTrackFromPlaylistService
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

    public function execute(string $playlistId, \stdClass $json, SymfonyStyle $io): void
    {
        $track = $json->track;
        if(!$track->id){
            $io->writeln(sprintf('track %s has no id', $track->name));
            return;
        }
        $this->addTrackService->execute($track, $io, $playlistId === '3Y6xdoDZ4vvad5dTVQd6KE');
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT track_id FROM track2playlist WHERE track_id = ? AND playlist_id = ?',
            [$track->id, $playlistId], [\PDO::PARAM_STR, \PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return;
        }
        $this->entityManager->getConnection()->executeQuery(
            'INSERT INTO track2playlist (track_id, playlist_id, added_at, created_at, track_number) VALUES (?,?,?,now(),?)',
            [
                $track->id,
                $playlistId,
                date('Y-m-d H:i:s', strtotime($json->added_at)),
                $track->track_number
            ],
            [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT
            ]
        );
    }
}
