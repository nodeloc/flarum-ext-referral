<?php

namespace Nodeloc\Referral;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\GroupSerializer;
use Nodeloc\Bonus\BonusListItem;
use Tobscure\JsonApi\Relationship;

class FreecodeListItemSerializer extends AbstractSerializer
{
    protected $type = 'freecode-list-items';


    protected function getDefaultAttributes($item): array
    {
        $attributes = [
            'amount' => (int)$item->amount,
            'days' => (int)$item->days,
        ];

        return $attributes;
    }

    public function group($item): ?Relationship
    {
        return $this->hasOne($item, GroupSerializer::class);
    }
}
