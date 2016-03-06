<?php
namespace Vss\OAuthExtensionBundle\Grant;

use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2ServerException;
use OAuth2\OAuth2;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\Security\EmailUserProvider;

/**
 * Class EmailGrant
 * @package Vss\OAuthExtensionBundle\Grant
 */
class EmailGrant implements GrantExtensionInterface
{

    /**
     * @var OAuthStorage
     */
    private $storage;

    /**
     * @var EmailUserProvider
     */
    private $userProvider;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * RoleGrant constructor.
     * @param OAuthStorage $storage
     * @param EmailUserProvider $userProvider
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(OAuthStorage $storage, EmailUserProvider $userProvider,
        EncoderFactoryInterface $encoderFactory) {

        $this->storage = $storage;
        $this->userProvider = $userProvider;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * This authentification is role based
     * @see OAuth2\IOAuth2GrantExtension::checkGrantExtension
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {

        if (!isset($inputData['email'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "email" parameter found');
        }
        if (!isset($inputData['password'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "password" parameter found');
        }

        $email = $inputData['email'];
        $password = $inputData['password'];

        // FOSUB required know.
        $user = $this->userProvider->loadUserByUsername($email);


        if (null === $user) {
            return false;
        }

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            return array(
                'data' => $user,
            );
        }
        return false;
    }
}
