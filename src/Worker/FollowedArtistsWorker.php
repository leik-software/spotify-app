<?php declare(strict_types=1);

namespace App\Worker;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FollowedArtistsWorker extends BaseWorker
{

    public function run(SymfonyStyle $io): void
    {
        $this->entityManager->getConnection()->executeQuery(
            'UPDATE artist SET follow = 0'
        );
        $lastArtist = null;
        $artistsCount=0;
        do{
            if($lastArtist){
                $items = $this->getApi()->getUserFollowedArtists(['after' => $lastArtist]);
            }else{
                $items = $this->getApi()->getUserFollowedArtists();
            }
            $ids=[];
            foreach ($items->artists->items as $item){
                $lastArtist = $item->id;
                $ids[] = $item->id;
                $artistsCount++;
            }
            $this->entityManager->getConnection()->executeQuery(
                'UPDATE artist SET follow = 1 WHERE id IN (?)', [$ids], [Connection::PARAM_STR_ARRAY]
            );
            if(!$items->artists->next){
                break;
            }
        }while(true);
        $io->note(
            sprintf('You follow %d artists', $artistsCount)
        );
    }

    public function priority(): int
    {
        return 10;
    }
}
