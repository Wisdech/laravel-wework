<?php

namespace XuDev\Wework\Model;

class Model
{
    protected array $origin;

    protected array $fillable;

    public function __construct(array $data)
    {
        $this->fill($data);
        $this->origin = $data;
    }

    /**
     * 获取原始数据
     */
    public function getOrigin(): array
    {
        return $this->origin;
    }

    /**
     * 模型转Array输出
     */
    public function toArray(): array
    {
        $array = [];

        array_map(function ($attributeName) use (&$array) {
            $array[$attributeName] = $this->$attributeName;
        }, $this->fillable);

        return $array;
    }

    protected function fill(array $data): void
    {
        array_map(function ($attributeName) use ($data) {
            $this->fillAttributes($attributeName, $data);
        }, $this->fillable);
    }

    private function fillAttributes(string $attributeName, array $data): void
    {
        if (array_key_exists($attributeName, $data)) {
            $this->$attributeName = $data[$attributeName];
        } else {
            $this->$attributeName = null;
        }
    }
}
