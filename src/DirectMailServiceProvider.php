<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

/**
 * Class DirectMailServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class DirectMailServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function boot()
    {
        $this->app['swift.transport']->extend('ali_cloud_direct_mail', function () {
            $config = $this->app['config']->get('services.ali_cloud_direct_mail', []);

            return new DirectMailTransport(new Client(), $config['access_key_secret'], $config);
        });
    }
}