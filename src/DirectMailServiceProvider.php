<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use SebastianKennedy\Exceptions\EmptyArgumentException;
use SebastianKennedy\Exceptions\InvalidConfigurationException;

/**
 * Class DirectMailServiceProvider.
 */
class DirectMailServiceProvider extends ServiceProvider
{
    const REQUIRE_CONFIG = ['access_key_id', 'access_key_secret', 'from_alias', 'account_name'];

    /**
     * Register the service provider.
     */
    public function boot()
    {
        $this->app['swift.transport']->extend('ali_cloud_direct_mail', function () {
            $config = $this->app['config']->get('services.ali_cloud_direct_mail', []);

            if (!$config) {
                throw new EmptyArgumentException("");
            }

            if (array_diff_key($config, self::REQUIRE_CONFIG) !== self::REQUIRE_CONFIG) {
                throw new InvalidConfigurationException("");
            }

            return new DirectMailTransport(new Client(), $config['access_key_secret'], $config);
        });
    }
}