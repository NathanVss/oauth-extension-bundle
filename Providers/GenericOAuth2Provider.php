<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 11:21
 */

namespace Vss\OAuthExtensionBundle\Providers;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Buzz\Message\Response;
use Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException;
use Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException;

/**
 * Class GenericOAuth2Provider
 * @package Vss\OAuthExtensionBundle\Providers
 */
abstract class GenericOAuth2Provider implements OAuth2ProviderInterface
{

    /**
     * @var array
     */
    private $format;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $name;

    /**
     * @var BrowserManager
     */
    private $browserManager;

    /**
     * GenericOAuth2Provider constructor.
     * @param BrowserManager $browserManager
     * @param array $options
     * @param string $name
     */
    public function __construct(BrowserManager $browserManager, array $options, $name) {

        $formatResolver = new OptionsResolver();
        $optionsResolver = new OptionsResolver();

        $this->setupOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->setupFormat($formatResolver);
        $this->format = $formatResolver->resolve();

        $this->name = $name;
        $this->browserManager = $browserManager;

    }

    /**
     * @inheritdoc
     */
    public function getUserInformations($accessToken) {

        $browser = $this->browserManager->getBrowser();;

        $params = [
            $this->format['token'] => $accessToken
        ];

        $response = $browser->get($this->options['user_url'] . '?' . http_build_query($params));

        return $this->handleUserInformationsResponse($response);
    }

    /**
     * @param Response $response
     * @return array
     * @throws FailedToGetUserInfoException
     */
    protected function handleUserInformationsResponse(Response $response) {

        if ($response->getStatusCode() != 200) {
            throw new FailedToGetUserInfoException(sprintf("Failed to get user informations for provider %s.",
                $this->name));
        }

        $data = json_decode($response->getContent(), true);
        if (!$data) {
            throw new FailedToGetUserInfoException(sprintf("Failed to get user informations for provider %s. Can't read response.",
                $this->name));
        }

        return [
            'id' => $data[$this->format['userId']],
            'data' => $data
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTokenFromCode($code) {

        $browser = $this->browserManager->getBrowser();

        $params = $this->buildCodeParams($code);

        $response = $browser->get($this->options['token_url'] . '?' . http_build_query($params));

        return $this->handleCodeResponse($response);
    }

    /**
     * @param Response $response
     * @return array
     * @throws FailExchangeCodeException
     */
    protected function handleCodeResponse(Response $response) {

        if ($response->getStatusCode() != 200) {
            throw new FailExchangeCodeException(sprintf("Failed to exchange code for a access token for provider %s. Error : %s",
                $this->name, $response->getContent()));
        }

        $data = json_decode($response->getContent(), true);
        if (!$data) {
            throw new FailExchangeCodeException(sprintf("Failed to exchange code for a access token for provider %s. Can't read response : %s",
                $this->name, $response->getContent()));
        }

        return $this->buildCodeResponse($data);
    }

    /**
     * @param array $data
     * @return array
     * @throws FailExchangeCodeException
     */
    protected function buildCodeResponse(array $data) {

        if (!isset($data[$this->format['token']])) {
            throw new FailExchangeCodeException(sprintf("Failed to exchange code for a access token for provider %s. Can't read %s.",
                $this->name, $this->format['token']));
        }
        if (!isset($data[$this->format['expiresIn']])) {
            throw new FailExchangeCodeException(sprintf("Failed to exchange code for a access token for provider %s. Can't read %s.",
                $this->name, $this->format['expiresIn']));
        }

        return [
            'accessToken' => $data[$this->format['token']],
            'expiresIn' => $data[$this->format['expiresIn']]
        ];
    }

    /**
     * @param $code
     * @return array
     */
    protected function buildCodeParams($code) {

        $params = [
            $this->format['clientId'] => $this->options['client_id'],
            $this->format['clientSecret'] => $this->options['client_secret'],
            $this->format['redirectUri'] => $this->options['redirect_uri'],
            $this->format['code'] => $code,
        ];

        return $params;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setup various options
     * @param OptionsResolver $optionsResolver
     */
    protected function setupOptions(OptionsResolver $optionsResolver) {

        $optionsResolver->setRequired([
            'client_id',
            'client_secret',
            'redirect_uri',
            'token_url',
            'user_url'
        ]);

    }

    /**
     * setup the parameters format
     * @param OptionsResolver $formatResolver
     */
    protected function setupFormat(OptionsResolver $formatResolver) {

        $formatResolver->setDefaults([
            'clientId' => 'client_id',
            'clientSecret' => 'client_secret',
            'redirectUri' => 'redirect_uri',
            'token' => 'access_token',
            'userId' => 'id',
            'expiresIn' => 'expires_in',
            'code' => 'code'
        ]);

    }

}