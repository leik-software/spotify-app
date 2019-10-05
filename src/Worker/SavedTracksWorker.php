<?php declare(strict_types=1);

namespace App\Worker;

use App\Service\AddTrackService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SavedTracksWorker extends BaseWorker
{
    /**
     * @var AddTrackService
     */
    private $addTrackService;

    public function __construct(EntityManagerInterface $entityManager, AddTrackService $addTrackService)
    {
        parent::__construct($entityManager);
        $this->addTrackService = $addTrackService;
    }

    public function run(SymfonyStyle $io): void
    {
        $this->entityManager->getConnection()->executeQuery(
            'UPDATE track SET saved=0'
        );
        $offset=0;
        $countSaved=0;
        do{
            $savedIds=[];
            $items = $this->getApi()->getMySavedTracks(['offset' => $offset]);
            foreach ($items->items as $item){
                $this->addTrackService->execute($item->track, $io);
                $savedIds[] = $item->track->id;
            }
            $countSaved += $this->entityManager->getConnection()->executeUpdate(
                'UPDATE track SET saved = 1 WHERE id IN (?)',
                [$savedIds],
                [Connection::PARAM_STR_ARRAY]
            );
            if(!$items->next){
                break;
            }
            $offset += 20;
        }while(true);
        $io->writeln(
            sprintf(
                '%d tracks saved', $countSaved
            )
        );
    }

    public function priority(): int
    {
        return 20;
    }
}
