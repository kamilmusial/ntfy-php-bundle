<?php

namespace Kamilmusial\NtfyPhpBundle\DependencyInjection;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Kamilmusial\NtfyPhp\Auth\AccessToken;
use Kamilmusial\NtfyPhp\Auth\Basic;
use Kamilmusial\NtfyPhp\Client;
use Kamilmusial\NtfyPhp\Config;
use Kamilmusial\NtfyPhp\Factory\GuzzleRequestFactory;
use Kamilmusial\NtfyPhpBundle\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;

class NtfyPhpExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new \Kamilmusial\NtfyPhpBundle\DependencyInjection\Configuration();
        $config  = $this->processConfiguration($configuration, $configs);

        $httpClient = new Definition(\GuzzleHttp\Client::class);
        $container->setDefinition('ntfy_php.client', $httpClient);

        $serializer = new Definition(Serializer::class);
        $serializerBuilder = new Definition(SerializerBuilder::class);
        $serializerBuilder->addMethodCall('create');
        $serializer->setFactory([$serializerBuilder, 'build']);
        $container->setDefinition('ntfy_php.serializer', $serializer);

        foreach ($config['clients'] as $clientName => $client) {
            $configClass = new Definition(Config::class);
            $configClass->addArgument($client['uri']);
            if (isset($client['auth'])) {
                $auth = match ($client['auth']['type']) {
                    'basic' => (new Definition(Basic::class))->setArguments([$client['auth']['username'], $client['auth']['password']]),
                    'token' => (new Definition(AccessToken::class))->addArgument($client['auth']['token']),
                    default => throw new InvalidArgumentException(sprintf('Invalid value for auth method (%s)', $client['auth']['type'])),
                };
                $configClass->addArgument($auth);
            }

            $requestFactory = new Definition(GuzzleRequestFactory::class);
            $container->setDefinition('ntfy_php.factory.request.' . $clientName, $requestFactory);
            $requestFactory->addArgument($configClass);

            $client = new Definition(Client::class);
            $client->setArguments([
                $httpClient,
                $requestFactory,
                $serializer,
            ]);

            $container->setDefinition('ntfy_php.client.' . $clientName, $client);
        }
    }
}