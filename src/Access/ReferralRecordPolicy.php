<?php

namespace Nodeloc\Referral\Access;

use Nodeloc\Referral\ReferralRecord;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

/**
 * 用户权限检查？似乎不需要
 */
class ReferralRecordPolicy extends AbstractPolicy
{
    public function can(User $actor, string $ability, ReferralRecord $model)
    {
        // See https://docs.flarum.org/extend/authorization.html#custom-policies for more information.
    }
}
