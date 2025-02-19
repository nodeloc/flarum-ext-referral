<?php

namespace Nodeloc\Referral;


use Flarum\Database\AbstractModel;
use Flarum\Group\Group;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $group_id
 * @property string $content
 * @property int $order
 *
 * @property Group $group
 */
class FreecodeListItem extends AbstractModel
{
    protected $table = 'freecode_list';
    public function group(): Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

}
