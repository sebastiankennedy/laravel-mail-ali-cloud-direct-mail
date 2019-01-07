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

## Learning

### 疑惑

- Laravel 基于 SwiftMailer 库提供邮件 API
    - SwiftMailer 库是什么？
    - Laravel 如何基于 SwiftMailer 提供邮件 API
- Laravel 如何实现纯文本邮件、Blade 模版引擎邮件、Markdown 格式邮件
- Laravel 发送邮件的整个流程

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
    
### 总结

> SwiftMailer 库是什么？

SwiftMailer 是一个免费、功能丰富的 PHP 邮件发送库。

> Laravel 如何基于 SwiftMailer 提供邮件 API

SwiftMailer 提供 Transport Pattern Implementation，可以让开发者自定义发送驱动。

> Laravel 如何实现纯文本邮件、Blade 模版引擎邮件、Markdown 格式邮件

// TODO

> Laravel 发送邮件的整个流程

在了解 Laravel 发送邮件的整个流程之前，我们先了解 Laravel 请求的生命周期。HTTP 请求从单一入口文件 `public/index.php` 进入，
`index.php` 文件加载 `Composer` 定义的自动加载器，加载 `bootstrap/app.php` 文件，创建一个 Laravel 服务容器实例。然后根据
HTTP 请求的类型，将 HTTP 请求分发到 Laravel 服务容器实例里对应的内核去处理；以 HTTP 内核为例，它会接收 HTTP 请求，然后返回 
HTTP 响应。在这个请求-响应的过程中，HTTP 内核将先去处理对应的程序配置：错误处理，日志记录，中间件验证，处理完后会加载应用程序的
服务提供者（Providers）。Laravel 的各种组件服务均由服务提供者提供，因此邮件服务也不例外，我们可以在文件 `confing/app.php` 
看到代码如下：
```php
'providers' => [
...
Illuminate\Mail\MailServiceProvider::class,
...
]
```

打开 `MailServiceProvider.php` 文件，看见代码如下：
```php
public function register()
{
    $this->registerSwiftMailer();
    $this->registerIlluminateMailer();
    $this->registerMarkdownRenderer();
}
```

- $this->registerSwiftMailer(); 注册 SwiftMailer 服务
- $this->registerIlluminateMailer(); 注册 Laravel Mailer 服务
- $this->registerMarkdownRenderer(); 注册 Markdown 渲染服务

可以重点关注 `$this->registerSwiftMailer()` 的代码
```php
/**
 * Register the Swift Mailer instance.
 *
 * @return void
 */
public function registerSwiftMailer()
{
    $this->registerSwiftTransport();

    // Once we have the transporter registered, we will register the actual Swift
    // mailer instance, passing in the transport instances, which allows us to
    // override this transporter instances during app start-up if necessary.
    $this->app->singleton('swift.mailer', function () {
        if ($domain = $this->app->make('config')->get('mail.domain')) {
            Swift_DependencyContainer::getInstance()
                            ->register('mime.idgenerator.idright')
                            ->asValue($domain);
        }

        return new Swift_Mailer($this->app['swift.transport']->driver());
    });
}

/**
 * Register the Swift Transport instance.
 *
 * @return void
 */
protected function registerSwiftTransport()
{
    $this->app->singleton('swift.transport', function () {
        return new TransportManager($this->app);
    });
}
```
阅读上面代码，我们可以得知容器绑定实例如下：

- `swift.transport` 对应实例 `TransportManager`
- `swift.mailer` 对应实例 `SwiftMailer`


我们比较关心 SwiftMailer 是如何驱动的，所以来看看 `TransportManager` 是如何运行的，在查看 `TransportManager` 源代码后，
我发现 `TransportManager` 是基于 SMTP 服务器进行发送邮件的，由于当前项目的邮件发送是使用阿里云的邮件服务，
是基于 HTTP API 的邮件服务，所以重点关注如何编写自定义驱动的代码，我们可以在 `TransportManager` 的父类 `Manager` 看见方法 `extend`：
```php
/**
 * Register a custom driver creator Closure.
 *
 * @param  string    $driver
 * @param  \Closure  $callback
 * @return $this
 */
public function extend($driver, Closure $callback)
{
    $this->customCreators[$driver] = $callback;

    return $this;
}
```

为了更好地去理解自定义驱动的编写，我注册了一个 Mailgun 账号，测试并查看 Mailgun 驱动是如何发送邮件的。在查看源代码之后，我发现
文件 `MailgunTransport` 以及其父类 `Transport` 和父类实现的接口 `Swift_Transport`，已经提供相关接口和实例。参考 `MailgunTransport`
代码，实现代码如下：

`DirectMailServiceProvider.php`
```php

```

`DirectMailTransport.php`
```php

```


