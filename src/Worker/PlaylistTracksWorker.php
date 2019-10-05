<?php declare(strict_types=1);

namespace App\Worker;

use App\Service\AddTrackFromPlaylistService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PlaylistTracksWorker extends BaseWorker
{

    /**
     * @var AddTrackFromPlaylistService
     */
    private $addTrackFromPlaylistService;

    public function __construct(EntityManagerInterface $entityManager, AddTrackFromPlaylistService $addTrackFromPlaylistService)
    {
        parent::__construct($entityManager);
        $this->addTrackFromPlaylistService = $addTrackFromPlaylistService;
    }

    public function run(SymfonyStyle $io): void
    {
        $playlistIds = (array)$this->entityManager->getConnection()->executeQuery(
            'SELECT id, name FROM playlist WHERE autoscan = 1 ORDER BY last_scan ASC '
        )->fetchAll(\PDO::FETCH_KEY_PAIR);
        foreach ($playlistIds as $playlistId => $name){
            $io->writeln(
                sprintf('Sync playlist %s', $name)
            );
            $offset=0;
            do{
                $items = $this->getApi()->getPlaylistTracks($playlistId, ['offset' => $offset, 'limit' => 100]);
                foreach ($items->items as $item){
                    $this->addTrackFromPlaylistService->execute($playlistId, $item, $io);
                }
                if(!$items->next){
                    break;
                }
                $offset += 100;
            }while(true);
            $this->entityManager->getConnection()->executeQuery(
                'UPDATE playlist SET last_scan = now() WHERE id = ?', [$playlistId], [\PDO::PARAM_STR]
            );
        }
    }

    public function priority(): int
    {
        return 2;
    }
}
