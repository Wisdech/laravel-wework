<?php

namespace Wisdech\Wework\Exception;

use Exception;

class WeworkException extends Exception
{
    public function __construct(int $code = 0, string $message = null)
    {
        if (!$message) {
            $message = match ($code) {
                6000 => '数据版本冲突',
                40001 => '不合法的secret参数',
                40003 => '无效的UserID',
                40004 => '不合法的媒体文件类型',
                40005 => '不合法的type参数',
                40006 => '不合法的文件大小',
                40007 => '不合法的media_id参数',
                40008 => '不合法的msgtype参数',
                40009 => '上传图片大小不是有效值',
                40011 => '上传视频大小不是有效值',
                40013 => '不合法的CorpID',
                40014 => '不合法的access_token',
                40016 => '不合法的按钮个数',
                40017 => '不合法的按钮类型',
                40018 => '不合法的按钮名字长度',
                40019 => '不合法的按钮KEY长度',
                40020 => '不合法的按钮URL长度',
                40022 => '不合法的子菜单级数',
                40023 => '不合法的子菜单按钮个数',
                40024 => '不合法的子菜单按钮类型',
                40025 => '不合法的子菜单按钮名字长度',
                40026 => '不合法的子菜单按钮KEY长度',
                40027 => '不合法的子菜单按钮URL长度',
                40029 => '不合法的oauth_code',
                40031 => '不合法的UserID列表',
                40032 => '不合法的UserID列表长度',
                40033 => '不合法的请求字符',
                40035, 40058 => '不合法的参数',
                40036 => '不合法的模板id长度',
                40037 => '无效的模板id',
                40039 => '不合法的url长度',
                40050 => 'chatid不存在',
                40054 => '不合法的子菜单url域名',
                40055 => '不合法的菜单url域名',
                40056 => '不合法的agentid',
                40057 => '不合法的callbackurl或者callbackurl验证失败',
                40059 => '不合法的上报地理位置标志位',
                40063 => '参数为空',
                40066 => '不合法的部门列表',
                40068 => '不合法的标签/标签组ID',
                40070 => '指定的标签范围节点全部无效',
                40071 => '不合法的标签名字',
                40072 => '不合法的标签名字长度',
                40073 => '不合法的openid',
                40074 => 'news消息不支持保密消息类型',
                40077 => '不合法的pre_auth_code参数',
                40078 => '不合法的auth_code参数',
                40080 => '不合法的suite_secret',
                40082 => '不合法的suite_token',
                40083 => '不合法的suite_id',
                40084 => '不合法的permanent_code参数',
                40085 => '不合法的的suite_ticket参数',
                40086 => '不合法的第三方应用appid',
                40088 => 'jobid不存在',
                40089 => '批量任务的结果已清理',
                60020 => '不安全的访问IP',
            };
        }

        parent::__construct("企业微信接口错误：[$code] $message", $code);
    }
}
