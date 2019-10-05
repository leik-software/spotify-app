<?php declare(strict_types=1);

namespace App\Worker;

use App\Service\AddTrackFromAlbumService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AlbumsWorker extends BaseWorker
{

    /**
     * @var AddTrackFromAlbumService
     */
    private $addTrackFromAlbumService;

    public function __construct(AddTrackFromAlbumService $addTrackFromAlbumService, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->addTrackFromAlbumService = $addTrackFromAlbumService;
    }

    public function run(SymfonyStyle $io): void
    {
        $albumIds = (array)$this->entityManager->getConnection()->executeQuery(
            'SELECT id, name FROM album WHERE scanned = 0 '
        )->fetchAll(\PDO::FETCH_KEY_PAIR);
        foreach ($albumIds as $albumId => $name){
            $io->writeln(
                sprintf('Sync album %s', $name)
            );
            $offset=0;
            do{
                $items = $this->getApi()->getAlbumTracks($albumId, ['offset' => $offset, 'limit' => 50]);
                foreach ($items->items as $item){
                    $this->addTrackFromAlbumService->execute($albumId, $item, $io);
                }
                if(!$items->next){
                    break;
                }
                $offset += 50;
            }while(true);
            $this->entityManager->getConnection()->executeQuery(
                'UPDATE album SET scanned=1 WHERE id = ?', [$albumId], [\PDO::PARAM_STR]
            );
        }
    }

    public function priority(): int
    {
        return 12;
    }
}
