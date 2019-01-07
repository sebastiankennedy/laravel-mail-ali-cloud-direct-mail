<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

/**
 * Class DirectMailServiceProvider.
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

            if (!$config) {
                throw new
            }

            if (array_keys(['access_key_id', 'access_key_secret', 'from_alias', 'account_name'], $config)) {
                // TODO 缺少必要配置参数
            }

            return new DirectMailTransport(new Client(), $config['access_key_secret'], $config);
        });
    }
}