<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{

    public function store(VerificationCodeRequest $request,EasySms $easySms)
    {
        $phone = $request->phone;

        //模拟环境
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $easySms->send($phone, [
                    'content'  =>  "【Lbbs社区】您的验证码是{$code}。如非本人操作，请忽略本短信"
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                return $this->response->errorInternal($message ?: '短信发送异常');
            }
        }

//        真实环境
//        //生成4位随机岁，左侧补0
//        $code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
//        try{
//            $reslut = $easySms->send($phone,[
//                'content' => "【薛加松】您的验证码是{$code}"
//                ]);
//        }catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
//            $message = $exception->getException('yunpian')->getMessage();
//            return $this->response->errorInternal($message ?: '短信发送异常');
//        }

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(10);
        //缓存验证码10分钟过期
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        return $this->response->array([
            'key' => $key,
            'expirAt_at' => $expiredAt->toDateString()
            ])->setStatusCode(201);
    }
}