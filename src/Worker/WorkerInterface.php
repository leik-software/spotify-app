<?php declare(strict_types=1);

namespace App\Worker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface WorkerInterface
{
    public function run(SymfonyStyle $io): void;
    public function priority(): int;
    public function canRun(InputInterface $input): bool;
}
