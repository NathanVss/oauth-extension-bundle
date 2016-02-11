<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 22:43
 */

namespace Vss\OAuthExtensionBundle\Security\OAuth;

use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Util\UserManipulator;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;

class OAuthFOSUBManager implements OAuthUserManagerInterface
{

    /**
     * @var UserManipulator
     */
    private $userManipulator;

    /**
     * @var FOSUserManager
     */
    private $userManager;

    /**
     * UserManager constructor.
     * @param UserManipulator $userManipulator
     * @param FOSUserManager $userManager
     */
    public function __construct(UserManipulator $userManipulator, FOSUserManager $userManager) {

        $this->userManipulator = $userManipulator;
        $this->userManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function getUserFromOAuthResponse($providerName, array $data)
    {

        $field = $providerName . 'Id';
        if ($user = $this->userManager->findUserBy([$field => $data['id']])) {
            return $user;
        }

        $user = $this->userManipulator->create($data['data']['name'], 'secret', '', true, false);
        $setter = "set" . ucfirst($providerName) . 'Id';
        $user->$setter($data['id']);
        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function updateAccessToken(UserInterface $user, $providerName, $token) {

        $providerName = ucfirst($providerName);
        $setter = "set{$providerName}AccessToken";
        $user->$setter($token);

        $this->userManager->updateUser($user);
    }
}