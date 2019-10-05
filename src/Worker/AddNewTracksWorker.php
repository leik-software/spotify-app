<?php declare(strict_types=1);

namespace App\Worker;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddNewTracksWorker extends BaseWorker
{
    private const PLAYLIST_NAME = '5daqSP0IH6ioryQfiisK05';

    public function run(SymfonyStyle $io): void
    {
        do{
            $trackIdsToAdd = $this->entityManager->getConnection()->executeQuery(
                'SELECT id FROM add_new_track LIMIT 10'
            )->fetchAll(\PDO::FETCH_COLUMN);
            if(!count($trackIdsToAdd)){
                break;
            }
            $this->getApi()->addPlaylistTracks(
                self::PLAYLIST_NAME,
                    $trackIdsToAdd
            );
            $this->entityManager->getConnection()->executeQuery(
                'DELETE FROM add_new_track WHERE id IN (?)',
                [
                    $trackIdsToAdd
                ],
                [Connection::PARAM_STR_ARRAY]
            );
        }while(true);
    }

    public function priority(): int
    {
        return 100;
    }
}
