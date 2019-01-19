<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use Swift_Mime_SimpleMessage;
use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;

/**
 * Class DirectMailTransport
 *
 * @package SebastianKennedy\LaravelMailAliCloudDirectMail
 */
class DirectMailTransport extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var ClientInterface
     */
    protected $client;
    /**
     * The Ali Cloud Access key Secret
     *
     * @var
     */
    protected $key;
    /**
     * The Ali Cloud Config
     *
     * @var
     */
    protected $config;
    /**
     * The Ali Cloud API Url
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new DirectMail transport instance.
     *
     * @param ClientInterface $client
     * @param                 $key
     * @param                 $config
     */
    public function __construct(ClientInterface $client, $key, $config)
    {
        $this->client = $client;
        $this->key = $key;
        $this->config = $config;
        $this->url = "https://dm.aliyuncs.com";
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param null                     $failedRecipients
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $to = $this->getTo($message);

        $message->setBcc([]);

        $payload = ['query' => $this->payload($message, $to)];
        $this->client->request('POST', $this->url, $payload);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the HTTP payload for sending the DirectMail message.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @param  string                    $to
     *
     * @return array
     */
    protected function payload(Swift_Mime_SimpleMessage $message, $to)
    {
        $parameters = [
            'Format' => array_get($this->config, 'format', 'JSON'),
            'Version' => array_get($this->config, 'version', '2015-11-23'),
            'RegionId' => array_get($this->config, 'region_id', 'cn-hangzhou'),
            'AccessKeyId' => array_get($this->config, 'access_key_id'),
            'SignatureNonce' => uniqid(),
            'SignatureMethod' => array_get($this->config, 'signature_method', 'HMAC-SHA1'),
            'SignatureVersion' => array_get($this->config, 'signature_version', '1.0'),
            'Timestamp' => date('Y-m-d\TH:i:s\Z'),
            'Action' => array_get($this->config, 'action', 'SingleSendMail'),
            'AddressType' => array_get($this->config, 'address_type', 1),
            'FromAlias' => array_get($this->config, 'from_alias'),
            'AccountName' => array_get($this->config, 'account_name'),
            'ToAddress' => $to,
            'Subject' => $message->getSubject(),
            'HtmlBody' => $message->getBody(),
            'ReplyToAddress' => "true",
            'ClickTrace' => array_get($this->config, 'click_trace', 0),
        ];

        // 1.a 排序
        ksort($parameters);
        $parameters = ['Signature' => $this->generateSignature($parameters)] + $parameters;

        return $parameters;
    }

    /**
     * Generate Signature
     *
     * @param $parameters
     *
     * @return string
     */
    public function generateSignature($parameters)
    {
        // 1.b 对每个请求参数的名称和值进行编码，得到规范化请求字符串
        $queryString = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);

        // 2.计算签名的字符串
        $signString = 'POST&';
        $signString .= rawurlencode('/').'&';
        $signString .= rawurlencode($queryString);

        // 3.计算 HMAC 值
        $apiKey = $this->key.'&';
        $hash = hash_hmac('sha1', $signString, $apiKey, true);

        // 4.计算签名值
        $signature = base64_encode($hash);

        return $signature;
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     *
     * @return string
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return $display ? $display." <{$address}>" : $address;
        })->values()->implode(',');
    }

    /**
     * Get all of the contacts for the message.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     *
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array)$message->getTo(), (array)$message->getCc(), (array)$message->getBcc()
        );
    }
}