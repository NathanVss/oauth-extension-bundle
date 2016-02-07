<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 28/01/16
 * Time: 12:02
 */

namespace Vss\OAuthExtensionBundle\Security\RoleAuth;

use Buzz\Message\Response;
use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;

/**
 * Class RoleTokenProvider
 * @package Vss\OAuthExtensionBundle\Security\RoleAuth
 */
class RoleTokenProvider
{
    /**
     * @var BrowserManager
     */
    private $browserManager;

    /**
     * RoleTokenProvider constructor.
     * @param BrowserManager $browserManager
     */
    public function __construct(BrowserManager $browserManager) {
        $this->browserManager = $browserManager;
    }


    /**
     * @param Response $response
     * @return mixed
     * @throws BadAuthentificationException
     */
    public function handleResponse(Response $response) {

        $data = json_decode($response->getContent(), true);

        if ($response->getStatusCode() != 200) {

            if (!$data) {

                $e = new BadAuthentificationException("Authentification request has failed : $response");
                $e->setOAuthErrorName("undefined");
                $e->setOAuthErrorDescription("Can't handle API response.");
                throw $e;
            }

            $e = new BadAuthentificationException(sprintf("OAuth exception. %s : %s", $data['error'], $data['error_description']));
            $e->setOAuthErrorName($data['error']);
            $e->setOAuthErrorDescription($data['error_description']);
            throw $e;
        }
        return $data;

    }


    /**
     * @param string $endpoint
     * @param array $params
     * @param string $role
     * @return RoleTokenStorage
     * @throws BadAuthentificationException
     */
    public function authentificate($endpoint, array $params, $role) {

        $browser = $this->browserManager->getBrowser();

        $params['required_role'] = $role;
        $params['grant_type'] = "http://oauth.vss.com/grants/role";

        $response = $browser->get($endpoint . '?' . http_build_query($params));

        try {
            $data = $this->handleResponse($response);
        } catch (BadAuthentificationException $e) {
            throw $e;
        }

        return [
            'accessToken' => $data['access_token'],
            'expiresIn' => $data['expires_in'],
            'refreshToken' => $data['refresh_token']
        ];
    }

}