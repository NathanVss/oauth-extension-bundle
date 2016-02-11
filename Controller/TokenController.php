<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 09/02/16
 * Time: 22:01
 */

namespace Vss\OAuthExtensionBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class TokenController
 * @package Buddyvote\OAuthBundle\Controller
 */
class TokenController extends Controller
{

    /**
     * @param  Request $request
     * @return Response
     *
     * @Route("/oauth/v2/token")
     * @Method({"GET", "POST"})
     */
    public function tokenAction(Request $request)
    {

        if (strstr($request->getContentType(), "json")) {
            $jsonParsed = json_decode($request->getContent(), true);
            if ($jsonParsed) {
                $request->request->add($jsonParsed);
            }
        }
        try {
            return $this->get('fos_oauth_server.server')->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}