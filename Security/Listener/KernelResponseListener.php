<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 14:56
 */

namespace Vss\OAuthExtensionBundle\Security\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Vss\OAuthExtensionBundle\Security\EmailAuth\EmailToken;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;

class KernelResponseListener
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * ExpiredListener constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelResponse(FilterResponseEvent $event) {

        if ($event->getResponse()->getStatusCode() != 200) {
            return;
        }
        $token = $this->tokenStorage->getToken();
        if (!($token instanceof RoleToken || $token instanceof EmailToken)) {
            return;
        }
        $accessToken = $token->getAccessToken();
        $refreshToken = $token->getRefreshToken();

        $script = <<<EOF
<script>
authBootstrap = {
    accessToken: "$accessToken", refreshToken: "$refreshToken"
};
</script>
EOF;

        $event->getResponse()->setContent(str_replace("</head>", $script . "</head>", $event->getResponse()->getContent()));

    }

}