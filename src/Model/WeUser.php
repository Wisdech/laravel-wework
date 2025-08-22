<?php

namespace XuDev\Wework\Model;

class WeUser extends Model
{
    protected array $fillable = [
        'userid',
        'gender',
        'avatar',
        'qr_code',
        'mobile',
        'email',
        'biz_mail',
        'address',
    ];
}
