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
use Vss\OAuthExtensionBundle\Security\Utils\EmailProvider;

/**
 * Class EmailGrant
 * @package Vss\OAuthExtensionBundle\Grant
 */
class EmailGrant implements GrantExtensionInterface
{
    /**
     * @var EmailProvider
     */
    private $userProvider;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    const ERROR_LOCKED = "UserLocked";
    const ERROR_INVALID_PASSWORD = "InvalidPassword";
    const ERROR_UNKNOWN_USER = "UnknownUser";

    /**
     * RoleGrant constructor.
     * @param EmailProvider $userProvider
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EmailProvider $userProvider, EncoderFactoryInterface $encoderFactory) {

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

        $user = $this->userProvider->loadByEmail($email);

        if (null === $user) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, self::ERROR_UNKNOWN_USER, 'User not found.');
        }

        $encoder = $this->encoderFactory->getEncoder($user);


        if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {

            if (method_exists($user, 'isLocked')) {

                if ($user->isLocked()) {
                    throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, self::ERROR_LOCKED, "User is locked.");
                }
            }

            return array(
                'data' => $user,
            );
        }
        throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, self::ERROR_INVALID_PASSWORD, 'Password is not valid.');
    }
}
