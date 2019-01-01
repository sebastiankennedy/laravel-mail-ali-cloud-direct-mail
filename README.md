<h1 align="center"> laravel-mail-ali-cloud-direct-mail </h1>

<p align="center">:e-mail: Ali Cloud Drirect Mail for Laravel Application.</p>


## Installing

```shell
$ composer require sebastiankennedy/laravel-mail-ali-cloud-direct-mail -vvv
```

## Usage

```php
# services.php`
[
...
'ali_cloud_direct_mail' => [
        'access_key_id' => env('ALI_CLOUD_DIRECT_MAIL_ACCESS_KEY_ID'),
        'access_key_secret' => env('ALI_CLOUD_DIRECT_MAIL_ACCESS_KEY_SECRET'),
        'format' => 'JSON',
        'version' => '2015-11-23',
        'region_id' => 'hangzhou',
        'address_type' => 1,
        'from_alias' => 'XXXX',
        'click_trace' => 0,
        'account_name' => 'XXXX@.com',
    ],
]

# usage
Mail::to($tutorCertification->email)->send($mailable);
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/sebastiankennedy/laravel-mail-ali-cloud-direct-mail/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/sebastiankennedy/laravel-mail-ali-cloud-direct-mail/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
