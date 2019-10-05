<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use App\Command\WorkerRunCommand;
use App\Worker\WorkerInterface;
use Assert\Assertion;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class WorkerCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        Assertion::true($container->hasDefinition(WorkerRunCommand::class));
        $definition = $container->getDefinition(WorkerRunCommand::class);
        $taggedWorker = $container->findTaggedServiceIds(WorkerInterface::class);
        foreach ($taggedWorker as $id => $worker){
            $definition->addMethodCall('addWorker', [new Reference($id)]);
        }
    }
}
