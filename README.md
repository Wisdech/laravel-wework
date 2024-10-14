# PHP 企业微信服务端接口实现

## 安装使用

```bash
#添加依赖
composer require wisdech/laravel-wework
```

## Laravel项目中

```bash
#发布配置文件
php artisan vendor:publish --tag=wework-config
```

```dotenv
#在.env文件填写配置信息
WEWORK_CORP_ID=
WEWORK_AGENT_ID=
WEWORK_SECRET=
```

```php
//使用Facade
use Wisdech\Wework\Facade\Wework

$loginUri=Wework::buildLoginUri(...);
```

## 在其他PHP项目中
```php
$sdk=new Wisdech\Wework\WeworkSDK('CorpID','AgentID','Secret')

$loginUri=$sdk->buildLoginUri(...);
```