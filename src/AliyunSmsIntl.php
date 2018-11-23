<?php

namespace Geekor\LaravelAliyunSmsIntl;

use Geekor\AliyunSmsIntlCore\Config;
use Geekor\AliyunSmsIntlCore\Profile\DefaultProfile;
use Geekor\AliyunSmsIntlCore\DefaultAcsClient;
use Geekor\AliyunSmsIntlCore\Regions\Endpoint;
use Geekor\AliyunSmsIntlCore\Regions\EndpointConfig;
use Geekor\AliyunSmsIntlCore\Regions\EndpointProvider;
use Geekor\AliyunSmsIntlCore\Exception\ClientException;
use Geekor\AliyunSmsIntlCore\Exception\ServerException;
use Geekor\AliyunSmsIntl\Sms\Request\V20180501\SendSmsRequest;

class AliyunSmsIntl {

    /**
     * Create a new AliyunSmsIntl instance.
     *
     * @return void
     */
    public function __construct()
    {
        Config::load();
    }

    /**
     * 发送短信
     *
     * @param mobile 手机号码，记得加上区域代号。
     *        例如：  "886123456789", 886 为地区代号，123456789 为手机号
     * @param tplId  模板 ID
     *        例如：  "SMS_00000001"
     * @param params 模板变量，数字一定要转换为字符串。
     *        例如：  array("code"=>"1234")
     */
    public function sendSms($mobile, $tplId, $params) {
        return $this->sendSmsExt($mobile, $tplId, $params, false, false);
    }

    /**
     * 发送短信 (带区域站点设置)
     */
    public function sendSmsExt($mobile, $tplId, $params, $region, $endPointName) {
        // product name， please remain unchanged
        $product = "Dysmsapi";
        // product domain, please remain unchanged
        $domain = "dysmsapi.ap-southeast-1.aliyuncs.com";

        // AccessKey and AccessKeySecret , you can login sms console and find it in API Management
        $accessKeyId = env('ALIYUN_SMS_ACCESS_KEY');
        $accessKeySecret = env('ALIYUN_SMS_ACCESS_SECRET');

        $region = !$region || "ap-southeast-1";
        $endPointName = !$endPointName || "ap-southeast-1";

        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        $acsClient = new DefaultAcsClient($profile);

        // initiate the SendSmsRequest, read help documents for more parameters instructions
        $request = new SendSmsRequest();

        // Optional, enable https
        //$request->setProtocol("https");

        // send to
        $request->setPhoneNumbers($mobile);

        // ContentCode , you can login sms console and find it in Content Management
        $request->setContentCode($tplId);

        // set the value for parameters in sms Content with JSON format. For example, the content is "Your Verification Code : ${code}, will be expired 5 minutes later"
        $request->setContentParam(json_encode($params, JSON_UNESCAPED_UNICODE));

        // Optional，custom field, this value will be returned in the sms delivery report.
        //$request->setExternalId("1234567");

        $acsResponse = $acsClient->getAcsResponse($request);

        /*
            $response = sendSms();
            echo "send sms response:\n";
            print_r($response);
        */

        return $acsResponse;
    }

    //
    // function send($mobile, $tplId, $params)
    // {
    //     defined('ENABLE_HTTP_PROXY') or define('ENABLE_HTTP_PROXY', env('ALIYUN_SMS_ENABLE_HTTP_PROXY', false));
    //     defined('HTTP_PROXY_IP') or define('HTTP_PROXY_IP',     env('ALIYUN_SMS_HTTP_PROXY_IP', '127.0.0.1'));
    //     defined('HTTP_PROXY_PORT') or define('HTTP_PROXY_PORT',   env('ALIYUN_SMS_HTTP_PROXY_PORT', '8888'));
    //
    //
    //     $endpoint = new Endpoint('cn-hangzhou', EndpointConfig::getregionIds(), EndpointConfig::getProducDomains());
    //     $endpoints = array($endpoint);
    //     EndpointProvider::setEndpoints($endpoints);
    //
    //     $iClientProfile = DefaultProfile::getProfile('cn-hangzhou', ENV('ALIYUN_ACCESS_KEY'), ENV('ALIYUN_ACCESS_SECRET'));
    //     app('sms.log')->info('config-info-pre:'.ENV('ALIYUN_ACCESS_KEY').'-'.ENV('ALIYUN_ACCESS_SECRET'));
    //     app('sms.log')->info('config-info:'.json_encode($iClientProfile));
    //     $client = new DefaultAcsClient($iClientProfile);
    //     //$request = new SingleSendSmsRequest();
    //     $request = new SendSmsRequest();
    //     $request->setSignName(ENV('ALIYUN_SMS_SIGN_NAME')); /*签名名称*/
    //     $request->setTemplateCode($tplId);                /*模板code*/
    //     $request->setRecNum($mobile);                     /*目标手机号*/
    //     $request->setParamString(json_encode($params));/*模板变量，数字一定要转换为字符串*/
    //
    //     try {
    //         $response = $client->getAcsResponse($request);
    //         return $response;
    //     } catch (ClientException  $e) {
    //         app('sms.log')->error('客户端错误');
    //         app('sms.log')->error('ErrorCode : '.$e->getErrorCode());
    //         app('sms.log')->error('ErrorMessage : '.$e->getErrorMessage());
    //     } catch (ServerException  $e) {
    //         app('sms.log')->error('服务端错误');
    //         app('sms.log')->error($e->getErrorCode());
    //         app('sms.log')->error($e->getErrorMessage());
    //     }
    //
    //     return false;
    // }

}
