<?php

namespace SebastianKennedy\LaravelMailAliCloudDirectMail;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class DirectMailTransport extends Transport
{
    protected $client;
    protected $accessKeySecret;
    protected $config;
    protected $url = "https://dm.aliyuncs.com/";
    protected $action = "SingleSendMail";
    const SIGNATURE_METHOD = 'HMAC-SHA1';
    const SIGNATURE_VERSION = "1.0";

    public function __construct(ClientInterface $client, $accessKeySecret, $config = [])
    {
        $this->client = $client;
        $this->accessKeySecret = $accessKeySecret;
        $this->config = $config;
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);
        $parameters = $this->formatParameters($message);
        $this->client->request('POST', $this->url, [
            'query' => $parameters,
        ]);
        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    public function formatParameters(Swift_Mime_SimpleMessage $message)
    {
        $parameters = array_merge($this->getCommonParameters(), $this->getActionParameters($message));
        ksort($parameters);
        $parameters = ['Signature' => $this->generateSignature($parameters)] + $parameters;

        return $parameters;
    }

    public function getCommonParameters()
    {
        return [
            'Format' => array_get($this->config, 'format', 'JSON'),
            'Version' => array_get($this->config, 'version', '2015-11-23'),
            'RegionId' => array_get($this->config, 'region_id', 'cn-hangzhou'),
            'AccessKeyId' => array_get($this->config, 'access_key_id'),
            'SignatureNonce' => uniqid(),
            'SignatureMethod' => self::SIGNATURE_METHOD,
            'SignatureVersion' => self::SIGNATURE_VERSION,
            'Timestamp' => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    public function getActionParameters(Swift_Mime_SimpleMessage $message)
    {
        if ($this->action === 'SingleSendMail') {
            return [
                'Action' => $this->action,
                'AddressType' => array_get($this->config, 'address_type', 1),
                'FromAlias' => array_get($this->config, 'from_alias'),
                'AccountName' => array_get($this->config, 'account_name'),
                'ToAddress' => $this->getMessageAddress($message),
                'Subject' => $message->getSubject(),
                'HtmlBody' => $message->getBody(),
                'ReplyToAddress' => "true",
                'ClickTrace' => array_get($this->config, 'click_trace', 0),
            ];
        }
    }

    public function generateSignature($parameters)
    {
        // 1.b 对每个请求参数的名称和值进行编码，得到规范化请求字符串
        $queryString = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);

        // 2.计算签名的字符串
        $signString = 'POST&';
        $signString .= rawurlencode('/').'&';
        $signString .= rawurlencode($queryString);

        // 3.计算 HMAC 值
        $apiKey = $this->accessKeySecret.'&';
        $hash = hash_hmac('sha1', $signString, $apiKey, true);

        // 4.计算签名值
        $signature = base64_encode($hash);

        return $signature;
    }

    public function getMessageAddress(Swift_Mime_SimpleMessage $message)
    {
        return head(array_keys(array_merge(
            (array)$message->getTo(), (array)$message->getCc(), (array)$message->getBcc()
        )));
    }
}