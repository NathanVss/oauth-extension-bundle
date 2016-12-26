<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 18/05/16
 * Time: 00:37
 */

namespace Vss\OAuthExtensionBundle\Security\EmailAuth;

use Buzz\Message\Response;
use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;

/**
 * Class EmailTokenProvider
 * @package Vss\OAuthExtensionBundle\Security\RoleAuth
 */
class EmailTokenProvider
{
    /**
     * @var BrowserManager
     */
    private $browserManager;

    /**
     * EmailTokenProvider constructor.
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
    public function authentificate($endpoint, array $params, $grant) {

        $browser = $this->browserManager->getBrowser();

        $params['grant_type'] = $grant;

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