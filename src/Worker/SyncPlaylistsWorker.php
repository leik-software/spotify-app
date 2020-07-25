<?php declare(strict_types=1);

namespace App\Worker;

use App\Entity\Playlist;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncPlaylistsWorker extends BaseWorker
{

    public function run(SymfonyStyle $io): void
    {
        $this->entityManager->getConnection()->executeUpdate(
            'UPDATE playlist SET autoscan = 0'
        );
        $playlists = $this->getApi()->getMyPlaylists(['limit'=>50]);
        foreach ($playlists->items as $playlist){
            if($playlist->owner->id === 'xyegc3dw53c4qgnophwvmwhbs' && $playlist->id !== '3Y6xdoDZ4vvad5dTVQd6KE'){
                continue; //eigene Playlists
            }
            $checkPlaylist = $this->entityManager->getConnection()->executeQuery('SELECT id, name FROM playlist WHERE id = ?', [$playlist->id],[\PDO::PARAM_STR])->fetch();
            if($checkPlaylist && $checkPlaylist['id']){
                $this->entityManager->getConnection()->executeQuery(
                    'UPDATE playlist SET name=?, autoscan=1 WHERE id=?',
                    [$playlist->name, $playlist->id],
                    [\PDO::PARAM_STR, \PDO::PARAM_STR]
                );
                $io->writeln(
                    sprintf(
                        'Playlist "%s" updated', $playlist->name
                    )
                );
                continue;
            }
            $this->entityManager->persist(new Playlist($playlist));
            $io->writeln(
                sprintf(
                    'New playlist "%s" created', $playlist->name
                )
            );
        }
        $this->entityManager->flush();
    }

    public function priority(): int
    {
        return 1;
    }
}
