<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\AddNewTrack;
use App\Helper\TrackHashHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddNewTrackService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(\stdClass $track, SymfonyStyle $io, bool $force = false): void
    {
        $replacements = [
            'Acoustic',
            'Chill Out',
            'ChillOut',
            'Remix',
            '- Dub',
            'ABGT',
            'Instrumental',
            ' VIP',
            'ASOT',
            '(Mixed)',
        ];
        if(!$force){
            foreach ($replacements as $replacement){
                if(stripos($track->name, $replacement)){
                    return;
                }
            }

            if($track->duration_ms < 2.5*60*1000){
                #$io->writeln('Track too short');
                return;
            }
            if($track->duration_ms > 10*60*1000){
                #$io->writeln('Track too long');
                return;
            }
        }
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT count(*) FROM track WHERE track_hash = ?',
            [TrackHashHelper::generateHash($track)],
            [\PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return;
        }
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT count(*) FROM add_new_track WHERE id = ?',
            [$track->id],
            [\PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return;
        }
        $this->entityManager->persist(
            new AddNewTrack($track->id, $track->name, TrackHashHelper::getArtistNames($track), $track->popularity ?? 0, $track->duration_ms/1000/60)
        );
        $this->entityManager->flush();
        $io->note(
            sprintf(
                'Track "%s - %s" will be added', TrackHashHelper::getArtistNames($track), $track->name
            )
        );
    }
}
