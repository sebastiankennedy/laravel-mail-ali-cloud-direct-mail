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
     * Necessary Configurations
     */
    protected $configurations = ['access_key_id', 'access_key_secret', 'from_alias', 'account_name'];
    /**
     * Response Format
     */
    protected $format = ['xml', 'json'];
    /**
     * Version
     */
    protected $version = ['2015-11-23', '2017-06-22'];
    /**
     * Region Id
     */
    protected $regionId = ['cn-hangzhou', 'ap-southeast-1', 'ap-southeast-2'];

    /**
     * @return mixed
     */
    public function boot()
    {
        $this->app['swift.transport']->extend('ali_cloud_direct_mail', function () {
            $config = array_filter($this->app['config']->get('services.ali_cloud_direct_mail', []));

            if (!$config) {
                throw new EmptyConfigException('ali cloud direct mail configuration not found.');
            }

            foreach ($this->configurations as $key) {
                if (!array_key_exists($key, $config)) {
                    throw new EmptyArgumentException($key.' cannot be empty');
                }

                if ($key === 'format' && !in_array(strtolower($config[$key]), $this->format, true)) {
                    throw new InvalidConfigException('Invalid format: '.$config[$key]);
                }

                if ($key === 'version' && !in_array(strtolower($config[$key]), $this->version, true)) {
                    throw new InvalidConfigException('Invalid version: '.$config[$key]);
                }

                if ($key === 'region_id' && !in_array(strtolower($config[$key]), $this->regionId, true)) {
                    throw new InvalidConfigException('Invalid region_id: '.$config[$key]);
                }
            }

            return new DirectMailTransport(new Client, $config['access_key_secret'], $config);
        });
    }
}