<?php

namespace Nodeloc\Referral\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Nodeloc\Referral\ReferralRecord;
use InvalidArgumentException;

class ReferralRecordSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'referral-records';

    protected $include = [
        'doorkeys', // 添加这一行
    ];

    public $optionalInclude = ['doorkeys','doorkeys.key', 'doorkeys.activates'];
    /**
     * {@inheritdoc}
     */
    // ReferralRecordSerializer

    public function getId($model)
    {
        // 如果是集合，则取出第一个记录的 id
        if ($model instanceof \Illuminate\Support\Collection) {
            $record = $model->first();
            return $record ? $record->id : null;
        }

        // 如果是单个记录，则返回记录的 id
        return parent::getId($model);
    }

    /**
     * {@inheritdoc}
     *
     * @param ReferralRecord $model
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
        // 如果是集合，则遍历集合中的每个记录并调用 parent::getDefaultAttributes
        if ($model instanceof \Illuminate\Support\Collection) {
            $attributes = [];

            foreach ($model as $record) {
                $attributes[] = $this->getSingleAttributes($record);
            }

            return $attributes;
        }

        if (!($model instanceof ReferralRecord)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . ReferralRecord::class
            );
        }

        // See https://docs.flarum.org/extend/api.html#serializers for more information.

        return [
            'id' => $model->id, // 使用 $model->id
            'user_id' => $model->user_id,
            'doorkey_id' => $model->doorkey_id,
            'key_cost' => $model->key_cost,
            'key_count' => $model->key_count,
            'actives' => $model->actives,
            'is_expire' => $model->is_expire,
            'created_at' => $this->formatDate($model->created_at),
            'updated_at' => $this->formatDate($model->updated_at),
        ];
    }

    protected function getSingleAttributes($model)
    {
        // 如果是报错
        if ($model instanceof \Exception) {
            return  [
                'error' => $model->getMessage()
            ];
        }

        if (!($model instanceof ReferralRecord)) {
            throw new InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . ReferralRecord::class
            );
        }

        // See https://docs.flarum.org/extend/api.html#serializers for more information.

        return [
            'id' => $model->id, // 使用 $model->id
            'user_id' => $model->user_id,
            'doorkey_id' => $model->doorkey_id,
            'key_cost' => $model->key_cost,
            'key_count' => $model->key_count,
            'actives' => $model->actives,
            'is_expire' => $model->is_expire,
            'created_at' => $this->formatDate($model->created_at),
            'updated_at' => $this->formatDate($model->updated_at),
            'doorkey' => $this->includeDoorkey($model), // 新加的行
        ];
    }
    protected function includeDoorkey($model)
    {
        $doorkey = $model->doorkey;
        if (!$doorkey) {
            return null;
        }

        return [
            'id' => $doorkey->id,
            'key' => $doorkey->key,
            'uses' => (int) $doorkey->uses,
            'groupId' => (int) $doorkey->group_id,
            'maxUses' => (int) $doorkey->max_uses,
            'activates' => (boolean) $doorkey->activates,
        ];
    }
}
