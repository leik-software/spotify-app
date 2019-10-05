<?php

namespace App\Command;

use App\Worker\WorkerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WorkerRunCommand extends Command
{
    protected static $defaultName = 'worker:run';

    /**
     * @var array
     */
    private $workerList=[];

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workerList[] = $worker;
    }

    protected function configure()
    {
        $this
            ->addOption(
                'incl',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        /** @var WorkerInterface $worker */
        try{
            $sortedWorker = [];

            foreach ($this->workerList as $worker){
                $sortedWorker[$worker->priority()][] = $worker;
            }
            ksort($sortedWorker);

            foreach ($sortedWorker as $priority => $workerList){
                foreach ($workerList as $worker) {
                    if(!$worker->canRun($input)){
                        continue;
                    }
                    $io->note(
                        sprintf('Running Worker %s', get_class($worker))
                    );
                    $worker->run($io);
                }
            }
        }catch (\Throwable $exception){
            $io->error($exception->getMessage());
            $io->writeln($exception->getTraceAsString());
        }
    }
}
