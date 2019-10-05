<?php declare(strict_types=1);

namespace App\Worker;

use App\Service\AddAlbumService;
use App\Service\AddTrackService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ArtistAlbumsWorker extends BaseWorker
{
    /**
     * @var AddAlbumService
     */
    private $addAlbumService;
    /**
     * @var AddTrackService
     */
    private $addTrackService;

    public function __construct(
        EntityManagerInterface $entityManager,
        AddAlbumService $addAlbumService,
        AddTrackService $addTrackService
    )
    {
        parent::__construct($entityManager);
        $this->addAlbumService = $addAlbumService;
        $this->addTrackService = $addTrackService;
    }

    public function run(SymfonyStyle $io): void
    {
        $followedArtits = $this->entityManager->getConnection()->executeQuery(
            'SELECT id, name FROM artist WHERE follow=1'
        )->fetchAll(\PDO::FETCH_KEY_PAIR);
        foreach ($followedArtits as $id => $name){
            $io->writeln(
                sprintf(
                    'Process artist %s', $name
                )
            );

            $this->addAlbumService->execute(
                $this->getApi()->getArtistAlbums($id, ['limit' => 20, 'include_groups' => 'single']),
                $io
            );
            $topTracks = $this->getApi()->getArtistTopTracks($id, ['country'=>'DE']);
            foreach ($topTracks->tracks as $track){
                $this->addTrackService->execute($track, $io);
            }
        }
    }

    public function priority(): int
    {
        return 11;
    }
}
