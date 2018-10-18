<?php

namespace Geekor\LaravelAliyunSmsIntl;

use Geekor\AliyunSmsIntlCore\Profile\DefaultProfile;
use Geekor\AliyunSmsIntlCore\DefaultAcsClient;
use Geekor\AliyunSmsIntlCore\Regions\Endpoint;
use Geekor\AliyunSmsIntlCore\Regions\EndpointConfig;
use Geekor\AliyunSmsIntlCore\Regions\EndpointProvider;
use Geekor\AliyunSmsIntlCore\Exception\ClientException;
use Geekor\AliyunSmsIntlCore\Exception\ServerException;

//use Geekor\AliyunSmsIntl\Sms\Request\V20180501\SingleSendSmsRequest;
use Geekor\AliyunSmsIntl\Sms\Request\V20180501\SendSmsRequest;

class AliyunSmsIntl {
    public function sendDemo() 
    {
        return "进来了";
    }

    public function send($mobile, $tplId, $params)
    {
        defined('ENABLE_HTTP_PROXY') or define('ENABLE_HTTP_PROXY', env('ALIYUN_SMS_ENABLE_HTTP_PROXY', false));
        defined('HTTP_PROXY_IP') or define('HTTP_PROXY_IP',     env('ALIYUN_SMS_HTTP_PROXY_IP', '127.0.0.1'));
        defined('HTTP_PROXY_PORT') or define('HTTP_PROXY_PORT',   env('ALIYUN_SMS_HTTP_PROXY_PORT', '8888'));


        $endpoint = new Endpoint('cn-hangzhou', EndpointConfig::getregionIds(), EndpointConfig::getProducDomains());
        $endpoints = array($endpoint);
        EndpointProvider::setEndpoints($endpoints);

        $iClientProfile = DefaultProfile::getProfile('cn-hangzhou', ENV('ALIYUN_ACCESS_KEY'), ENV('ALIYUN_ACCESS_SECRET'));
        app('sms.log')->info('config-info-pre:'.ENV('ALIYUN_ACCESS_KEY').'-'.ENV('ALIYUN_ACCESS_SECRET'));
        app('sms.log')->info('config-info:'.json_encode($iClientProfile));
        $client = new DefaultAcsClient($iClientProfile);
        //$request = new SingleSendSmsRequest();
        $request = new SendSmsRequest();
        $request->setSignName(ENV('ALIYUN_SMS_SIGN_NAME')); /*签名名称*/
        $request->setTemplateCode($tplId);                /*模板code*/
        $request->setRecNum($mobile);                     /*目标手机号*/
        $request->setParamString(json_encode($params));/*模板变量，数字一定要转换为字符串*/

        try {
            $response = $client->getAcsResponse($request);
            return $response;
        } catch (ClientException  $e) {
            app('sms.log')->error('客户端错误');
            app('sms.log')->error('ErrorCode : '.$e->getErrorCode());
            app('sms.log')->error('ErrorMessage : '.$e->getErrorMessage());
        } catch (ServerException  $e) {
            app('sms.log')->error('服务端错误');
            app('sms.log')->error($e->getErrorCode());
            app('sms.log')->error($e->getErrorMessage());
        }

        return false;
    }

}
