<?php

namespace ImDong\BuyDoorman\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use ImDong\BuyDoorman\BuyDoormanRecord;
use InvalidArgumentException;

class BuyDoormanRecordSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'buy-doorman-records';

    /**
     * {@inheritdoc}
     *
     * @param BuyDoormanRecord $model
     * @throws InvalidArgumentException
     */
    protected function getDefaultAttributes($model)
    {
        // 如果是报错
        if ($model instanceof \Exception) {
           return  [
                'error' => $model->getMessage()
            ];
        }

        if (! ($model instanceof BuyDoormanRecord)) {
            throw new InvalidArgumentException(
                get_class($this).' can only serialize instances of '.BuyDoormanRecord::class
            );
        }

        // See https://docs.flarum.org/extend/api.html#serializers for more information.

        return [
            'use_money' => $model->money,
        ];
    }
}
