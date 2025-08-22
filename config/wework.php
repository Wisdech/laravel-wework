<?php

return [

    /** 企业 CorpID */
    'corp_id' => env('WEWORK_CORP_ID'),

    /** 应用 AgentID */
    'agent_id' => env('WEWORK_AGENT_ID'),

    /** 应用 Secret */
    'secret' => env('WEWORK_SECRET'),

    /** Callback PATH */
    'redirect_uri' => env('WEWORK_REDIRECT_URI', '/wework/callback'),

    /** 收发消息 Token */
    'message_token' => env('WEWORK_MESSAGE_TOKEN'),

    /** 收发消息 EncodingAESKey */
    'encoding_aes_key' => env('WEWORK_ENCODING_AES_KEY'),
];
