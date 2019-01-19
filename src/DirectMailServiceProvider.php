<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use SebastianKennedy\LaravelMailAliCloudDirectMail\Exceptions\EmptyArgumentException;
use SebastianKennedy\LaravelMailAliCloudDirectMail\Exceptions\EmptyConfigException;
use SebastianKennedy\LaravelMailAliCloudDirectMail\Exceptions\InvalidConfigException;

/**
 * Class DirectMailServiceProvider.
 */
class DirectMailServiceProvider extends ServiceProvider
{
    /**
     * Necessary Config
     */
    const CONFIG_KEYS = ['access_key_id', 'access_key_secret', 'from_alias', 'account_name'];
    /**
     * Response Format
     */
    const FORMAT = ['xml', 'json'];
    /**
     * Version
     */
    const VERSION = ['2015-11-23', '2017-06-22'];
    /**
     * Region Id
     */
    const REGION_ID = ['cn-hangzhou', 'ap-southeast-1', 'ap-southeast-2'];

    /**
     * Register the service provider.
     */
    public function boot()
    {
        $this->app['swift.transport']->extend('ali_cloud_direct_mail', function () {
            $config = array_filter($this->app['config']->get('services.ali_cloud_direct_mail', []));

            if (!$config) {
                throw new EmptyConfigException("ali cloud direct mail configuration not found.");
            }

            foreach (self::CONFIG_KEYS as $key) {
                if (!array_key_exists($key, $config)) {
                    throw new EmptyArgumentException("{$key} cannot be empty.");
                }

                if ($key === 'format' && !in_array(strtolower($config[$key]), self::FORMAT)) {
                    throw new InvalidConfigException("Invalid format: ".$config[$key]);
                }

                if ($key === 'version' && !in_array(strtolower($config[$key]), self::VERSION)) {
                    throw new InvalidConfigException("Invalid version: ".$config[$key]);
                }

                if ($key === 'region_id' && !in_array(strtolower($config[$key]), self::REGION_ID)) {
                    throw new InvalidConfigException("Invalid region_id: ".$config[$key]);
                }
            }

            return new DirectMailTransport(new Client, $config['access_key_secret'], $config);
        });
    }
}