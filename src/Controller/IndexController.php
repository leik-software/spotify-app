<?php declare(strict_types=1);
namespace App\Controller;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/spotify/index", name="spotify-index")
 */
final class IndexController extends AbstractController
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }


    public function __invoke(string $clientId, string $clientSecret): Response
    {
        $session = new Session(
            $clientId,
            $clientSecret,
            $this->router->generate('spotify-index',[],Router::ABSOLUTE_URL)
        );

        $api = new SpotifyWebAPI();

        if (isset($_GET['code'])) {
            $session->requestAccessToken($_GET['code']);
            $api->setAccessToken($session->getAccessToken());
            $this->entityManager->getConnection()->executeQuery('DELETE FROM webtoken');
            $webToken = new Token($session->getAccessToken());
            $this->entityManager->persist($webToken);
            $this->entityManager->flush();
            print_r($session->getAccessToken());

        } else {
            $options = [
                'scope' => [
                    'user-read-email,user-follow-read,playlist-modify-public,playlist-read-private,playlist-modify-private,user-library-read',
                ],
            ];

            header('Location: ' . $session->getAuthorizeUrl($options));
            die();
        }
        return $this->render('index.html.twig');
    }
}
