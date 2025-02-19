<?php

namespace Nodeloc\Referral\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Nodeloc\Referral\Api\Serializer\ReferralRecordSerializer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\Document;
use Nodeloc\Referral\ReferralRecord;
use Flarum\User\User;
use Carbon\Carbon;

class LastFreeCodeController extends AbstractShowController
{
    public $serializer = ReferralRecordSerializer::class;

    protected function isSuspended(User $user): bool{
        return $user->suspended_until !== null
            && $user->suspended_until instanceof Carbon
            && $user->suspended_until->isFuture();
    }
    protected function data(Request $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        if ($actor->isGuest() || $this->isSuspended($actor)) {
            throw new \Flarum\User\Exception\PermissionDeniedException();
        }

        // 获取权限最高的用户组
        $group = $actor->groups()->orderBy('read_permission', 'desc')->first();
        if (!$group) {
            throw new \Exception("No group found for user.");
        }

        // 获取最后一个免费邀请码领取记录
        $lastRecord = ReferralRecord::where('user_id', $actor->id)
            ->where('key_cost', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastRecord ?: ReferralRecord::make([
            'id' => null,
            'created_at' => null,
            'key_cost' => 0,
            'key_count' => 0,
            'actives' => 0,
            'registers' => 0,
            'is_expire' => 0,
            'user_id' => $actor->id,
            'doorkey_id' => null,
        ]);

    }
}
