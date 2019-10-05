<?php declare(strict_types=1);

namespace App\Worker;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\Console\Input\InputInterface;

abstract class BaseWorker implements WorkerInterface
{
    /**
     * @var SpotifyWebAPI
     */
    private $api;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getApi(): SpotifyWebAPI
    {
        if($this->api instanceof SpotifyWebAPI){
            return $this->api;
        }
        /** @var Token $token */
        $token = current($this->entityManager->getRepository(Token::class)->findAll());
        $this->api = new SpotifyWebAPI();
        $this->api->setAccessToken($token->token());
        return $this->api;

    }

    public function canRun(InputInterface $input): bool
    {
        $options = $input->getOptions();
        if (0 === \count($options['incl'])) {
            return true;
        }
        $namespace = \get_class($this);
        $path = explode('\\', $namespace);
        $currentClass = str_replace('Worker', '', array_pop($path));
        if (!\in_array($currentClass, $options['incl'], true)) {
            return false;
        }

        return true;
    }
}
