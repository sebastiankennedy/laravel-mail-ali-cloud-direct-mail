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
        'format' => env('ALI_CLOUD_DIRECT_MAIL_FORMAT', 'JSON'),
        'version' => env('ALI_CLOUD_DIRECT_MAIL_VERSION', ''2015-11-23''),
        'region_id' => env('ALI_CLOUD_DIRECT_MAIL_REGION_ID', 'hangzhou'),
        'address_type' => env('ALI_CLOUD_DIRECT_MAIL_ADDRESS_TYPE', 1),
        'from_alias' => env('ALI_CLOUD_DIRECT_MAIL_FROM_ALIAS', 'FromAliasName')',
        'click_trace' => env('ALI_CLOUD_DIRECT_MAIL_CLICK_TRACE', 0),
        'account_name' => env('ALI_CLOUD_DIRECT_MAIL_ACCOUNT_NAME', 'account@name.com'),
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

## Learning

### 疑惑

- Laravel 基于 SwiftMailer 库提供邮件 API
    - SwiftMailer 库是什么？
    - Laravel 如何基于 SwiftMailer 提供邮件 API
- Laravel 如何实现纯文本邮件、Blade 模版引擎邮件、Markdown 格式邮件
- Laravel 发送邮件的生命周期

### 概念

- 在 Laravel 中，每封邮件都可以表示为「可邮寄」类。
- 在 Laravel 中，所有邮件模版都能够获取到 `$message` 变量。

### 配置

- 发件人
    - 在「可邮寄」类 `build` 方法中调用 `from`
    - 在 `config/mail.php` 文件中
- 视图
    - 使用 Blade 模版
        - 在「可邮寄」类 `build` 方法中调用 `view`
    - 使用纯文本
        - 在「可邮寄」类 `build` 方法中调用 `text`
    - 使用 Markdown 格式    
- 视图数据
    - 在「可邮寄」类中声明公共属性
    - 在「可邮寄」类 `build` 方法中调用 `with`
- 附件
    - 一般附件
        - 在「可邮寄」类 `build` 方法中调用 `attach`
    - 原始数据附件
        - 在「可邮寄」类 `build` 方法中调用 `attachData`
    - 内部附件
        - 在邮件模版 `$message` 变量调用 `embed`
    - 内部原始数据附件
        - 在邮件模版 `$message` 变量调用 `embedData`   
- Markdown 格式的 Mailable 类    

### 服务

- 队列服务
    - 默认队列
    - 指定队列
    - 延时队列
- 本地开发
    - 日志驱动
    - Mailtrap
- 事件
    - Laravel 会在发送邮件消息之前触发一个事件