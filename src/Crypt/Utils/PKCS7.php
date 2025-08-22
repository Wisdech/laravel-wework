<?php

namespace XuDev\Wework\Crypt\Utils;

class PKCS7
{
    public int $block_size = 32;

    /**
     * 对需要加密的明文进行填充补位
     *
     * @param  string  $text  需要进行填充补位操作的明文
     * @return string 补齐明文字符串
     */
    public function encode(string $text): string
    {
        $block_size = $this->block_size;
        $text_length = strlen($text);

        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }

        $pad_chr = chr($amount_to_pad);
        $tmp = str_repeat($pad_chr, $amount_to_pad);

        return $text.$tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     *
     * @param  string  $text  decrypted 解密后的明文
     * @return string 删除填充补位后的明文
     */
    public function decode(string $text): string
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > $this->block_size) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }
}
