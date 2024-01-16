<?php

namespace Kamilmusial\NtfyPhpBundle;

use Kamilmusial\NtfyPhpBundle\DependencyInjection\NtfyPhpExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NtfyPhpBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new NtfyPhpExtension();
    }
}