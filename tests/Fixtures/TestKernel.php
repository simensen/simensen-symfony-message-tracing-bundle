<?php

declare(strict_types=1);

namespace Simensen\SymfonyMessageTracingBundle\Tests\Fixtures;

use Simensen\SymfonyMessageTracingBundle\SimensenSymfonyMessageTracingBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private array $bundleConfig = [];

    public function __construct()
    {
        parent::__construct('test', true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SimensenSymfonyMessageTracingBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'test-secret',
                'messenger' => [
                    'default_bus' => 'messenger.bus.default',
                    'buses' => [
                        'messenger.bus.default' => null,
                    ],
                ],
            ]);

            // Load bundle configuration if provided
            if (!empty($this->bundleConfig)) {
                $container->loadFromExtension('simensen_symfony_message_tracing', $this->bundleConfig);
            }
        });
    }

    protected function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container): void
    {
        // Add a compiler pass to make services public for testing
        $container->addCompilerPass(new class implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface {
            public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container): void
            {
                $serviceIds = [
                    'simensen_message_tracing.trace_stack',
                    'simensen_message_tracing.trace_generator', 
                    'simensen_message_tracing.identity.generator',
                    'simensen_message_tracing.messenger.middleware.causation',
                    'simensen_message_tracing.messenger.middleware.correlation',
                    'simensen_message_tracing.middleware.causation',
                    'simensen_message_tracing.middleware.correlation',
                    'messenger.bus.default',
                ];

                foreach ($serviceIds as $serviceId) {
                    if ($container->hasDefinition($serviceId)) {
                        $container->getDefinition($serviceId)->setPublic(true);
                    }
                }

                // Make aliases public for testing
                $aliases = [
                    'Simensen\\MessageTracing\\TraceStack\\TraceStack',
                    'Simensen\\MessageTracing\\Trace\\TraceGenerator', 
                    'Simensen\\MessageTracing\\TraceIdentity\\TraceIdentityGenerator',
                ];

                foreach ($aliases as $aliasId) {
                    if ($container->hasAlias($aliasId)) {
                        $container->getAlias($aliasId)->setPublic(true);
                    }
                }
            }
        });
    }

    /**
     * Set bundle configuration for testing different scenarios.
     */
    public function setBundleConfig(array $config): void
    {
        $this->bundleConfig = $config;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/test_kernel_' . md5(__FILE__) . '/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/test_kernel_' . md5(__FILE__) . '/logs';
    }
}