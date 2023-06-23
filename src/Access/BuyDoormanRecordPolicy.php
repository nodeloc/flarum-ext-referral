<?php

namespace ImDong\BuyDoorman\Access;

use ImDong\BuyDoorman\BuyDoormanRecord;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

/**
 * 用户权限检查？似乎不需要
 */
class BuyDoormanRecordPolicy extends AbstractPolicy
{
    public function can(User $actor, string $ability, BuyDoormanRecord $model)
    {
        // See https://docs.flarum.org/extend/authorization.html#custom-policies for more information.
    }
}
