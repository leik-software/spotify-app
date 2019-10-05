<?php declare(strict_types=1);

namespace App\Worker;

use App\Entity\Playlist;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncPlaylistsWorker extends BaseWorker
{

    public function run(SymfonyStyle $io): void
    {
        $playlists = $this->getApi()->getMyPlaylists();
        foreach ($playlists->items as $playlist){
            $checkId = $this->entityManager->getConnection()->executeQuery('SELECT id FROM playlist WHERE id = ?', [$playlist->id],[\PDO::PARAM_STR])->fetchColumn();
            if($checkId){
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
