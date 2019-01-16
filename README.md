<h1 align="center"> laravel-mail-ali-cloud-direct-mail </h1>

<p align="center">:e-mail: Ali Cloud Drirect Mail for Laravel Application.</p>


## Installing

```shell
$ composer require sebastiankennedy/laravel-mail-ali-cloud-direct-mail -vvv
```

## Usage

```php
# .env
MAIL_DRIVER=directmail

# services.php
[
    'ali_cloud_direct_mail' => [
        'access_key_id' => env('ALI_CLOUD_DIRECT_MAIL_ACCESS_KEY_ID'),
        'access_key_secret' => env('ALI_CLOUD_DIRECT_MAIL_ACCESS_KEY_SECRET'),
        'from_alias' => env('ALI_CLOUD_DIRECT_MAIL_FROM_ALIAS'),
        'account_name' => env('ALI_CLOUD_DIRECT_MAIL_ACCOUNT_NAME'),
        'format' => env('ALI_CLOUD_DIRECT_MAIL_FORMAT', 'JSON'),
        'version' => env('ALI_CLOUD_DIRECT_MAIL_VERSION', '2015-11-23'),
        'region_id' => env('ALI_CLOUD_DIRECT_MAIL_REGION_ID', 'hangzhou'),
        'click_trace' => env('ALI_CLOUD_DIRECT_MAIL_CLICK_TRACE', 0),
        'address_type' => env('ALI_CLOUD_DIRECT_MAIL_ADDRESS_TYPE', 1),
    ],
]

# usage
Mail::to($email)->send($mailable);
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/sebastiankennedy/laravel-mail-ali-cloud-direct-mail/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/sebastiankennedy/laravel-mail-ali-cloud-direct-mail/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT


