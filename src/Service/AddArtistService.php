<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddArtistService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(\stdClass $json, SymfonyStyle $io): bool
    {
        $check = $this->entityManager->getConnection()->executeQuery(
            'SELECT id FROM artist WHERE id = ?', [$json->id], [\PDO::PARAM_STR]
        )->fetchColumn();
        if($check){
            return false;
        }
        $this->entityManager->persist(
            new Artist($json->id, $json->name)
        );
        $io->writeln(
            sprintf(
                'Artist %s added', $json->name
            )
        );
        $this->entityManager->flush();
        return true;
    }
}
