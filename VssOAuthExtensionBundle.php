<?php

namespace Vss\OAuthExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vss\OAuthExtensionBundle\DependencyInjection\VssOAuthExtensionExtension;

class VssOAuthExtensionBundle extends Bundle
{
    public function __construct()
    {
        $this->extension = new VssOAuthExtensionExtension();
    }
}
