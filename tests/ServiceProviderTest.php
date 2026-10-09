<?php

namespace Overtrue\LaravelPayment\Tests;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use Overtrue\LaravelPayment\Facade;
use Overtrue\LaravelPayment\Manager;
use Overtrue\LaravelPayment\ServiceProvider;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('payments', [
            'default_gateway' => 'dummy',
            'gateways' => [
                'dummy' => ['driver' => 'LaravelPayment_Test', 'options' => ['username' => 'local-only']],
            ],
        ]);
    }

    public function testDeferredProviderResolvesSingletonAndAlias(): void
    {
        $this->assertInstanceOf(DeferrableProvider::class, new ServiceProvider($this->app));
        $this->assertSame([Manager::class, 'payment'], (new ServiceProvider($this->app))->provides());
        $manager = $this->app->make('payment');
        $this->assertSame($manager, $this->app->make(Manager::class));
        $this->assertSame($manager, $this->app->make('payment'));
        $this->assertSame('local-only', $manager->gateway()->getUsername());
    }

    public function testFacadeResolvesSameManagerAndForwardsCalls(): void
    {
        $this->assertSame($this->app->make(Manager::class), Facade::getFacadeRoot());
        $this->assertSame(Facade::gateway(), $this->app->make('payment')->gateway());
        $this->assertSame(['first', ['second'], null], Facade::forwardArguments('first', ['second'], null));
    }

    public function testConfigCanBePublished(): void
    {
        $target = config_path('payments.php');

        try {
            $this->artisan('vendor:publish', [
                '--provider' => ServiceProvider::class,
                '--tag' => 'config',
                '--force' => true,
            ])->assertExitCode(0);
            $this->assertFileExists($target);
            $this->assertSame(file_get_contents(__DIR__.'/../config/payments.php'), file_get_contents($target));
        } finally {
            @unlink($target);
        }
    }

    public function testPackageDiscoveryMetadata(): void
    {
        $directory = sys_get_temp_dir().'/laravel-payment-'.bin2hex(random_bytes(8));
        $files = new Filesystem();
        $files->makeDirectory($directory.'/vendor/composer', 0755, true);
        $composer = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);

        try {
            $files->put($directory.'/vendor/composer/installed.json', json_encode(['packages' => [$composer]]));
            $manifest = new PackageManifest($files, $directory, $directory.'/packages.php');
            $this->assertSame([ServiceProvider::class], $manifest->providers());
            $this->assertSame(['LaravelPayment' => Facade::class], $manifest->aliases());
            $loader = \Illuminate\Foundation\AliasLoader::getInstance($manifest->aliases());
            $loader->register();
            $this->assertSame($this->app->make(Manager::class), \LaravelPayment::getFacadeRoot());
            $this->assertSame('local-only', \LaravelPayment::gateway()->getUsername());
        } finally {
            $files->deleteDirectory($directory);
        }
    }

    public function testLaravelRegistersProviderAsDeferred(): void
    {
        $directory = sys_get_temp_dir().'/laravel-payment-provider-'.bin2hex(random_bytes(8));
        $files = new Filesystem();
        $files->makeDirectory($directory);

        try {
            $app = new \Illuminate\Foundation\Application($directory);
            $app->instance('config', $this->app['config']);
            $repository = new \Illuminate\Foundation\ProviderRepository($app, $files, $directory.'/services.php');
            $repository->load([ServiceProvider::class]);

            $this->assertTrue($app->isDeferredService(Manager::class));
            $this->assertTrue($app->isDeferredService('payment'));
            $this->assertNull($app->getProvider(ServiceProvider::class));
            $manager = $app->make('payment');
            $this->assertSame($manager, $app->make(Manager::class));
            $this->assertSame('local-only', $manager->gateway()->getUsername());
        } finally {
            $files->deleteDirectory($directory);
        }
    }

    public function testPublishedConfigIsCacheable(): void
    {
        $this->app->make(Manager::class);
        $config = require __DIR__.'/../config/payments.php';
        $path = sys_get_temp_dir().'/laravel-payment-config-'.bin2hex(random_bytes(8)).'.php';

        try {
            file_put_contents($path, '<?php return '.var_export($config, true).';');
            $cached = require $path;
            $this->assertSame($config, $cached);
            $cached['default_gateway'] = 'dummy';
            $cached['gateways']['dummy'] = ['driver' => 'LaravelPayment_Test'];
            $manager = new Manager($cached);
            $this->assertTrue($manager->gateway()->getTestMode());
        } finally {
            @unlink($path);
        }
    }
}
