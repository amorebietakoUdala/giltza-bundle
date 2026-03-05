<?php

namespace AMREU\GiltzaBundle\Controller;

use AMREU\GiltzaBundle\Service\GiltzaProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GiltzaController extends AbstractController
{

    public function __construct(
        private GiltzaProvider $provider,
        private array $options,
    )
    {
    }

    #[Route(path: '/giltza', name: 'amreu_giltza_login')]
    public function giltza(Request $request): Response
    {
        $locale = $request->getSession()->get("_locale",$request->getDefaultLocale());
        $this->options['ui_locales'] = $locale;
        // If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {
            $authorizationUrl = $this->provider->getAuthorizationUrl($this->options);
            $_SESSION['oauth2state'] = $this->provider->getState();
            header('Location: ' . $authorizationUrl);
            exit;
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            exit('Invalid State');
        } else {
            try {
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $resourceOwner = $this->provider->getResourceOwner($accessToken);
                $authenticatedRequest = $this->provider->getAuthenticatedRequest(
                    'GET',
                    $this->provider->getResourceOwnerDetailsUrl($accessToken),
                    $accessToken
                );
                if (!$accessToken->hasExpired()) {
                    $response = $this->provider->getParsedResponse($authenticatedRequest);
                    $request->getSession()->set(
                        "giltzaUser",
                        $response
                    );
                    return $this->redirectToRoute($this->options['successUri']);
                } else {
                    return $this->redirectToRoute('amreu_giltza_login');
                }
            } catch (IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }

    #[Route(path: '/giltza/success', name: 'amreu_giltza_success')]
    public function success(Request $request): Response
    {
        $giltzaUser = $request->getSession()->get("giltzaUser");
        if (!$giltzaUser) {
            return $this->redirectToRoute('amreu_giltza_login');
        }
        return $this->json($giltzaUser);
    }

    #[Route(path: '/logout', name: 'amreu_giltza_logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        return $this->json('logout');
    }
}
