<?php

/*
 * This file is part of nodeloc/flarum-ext-referral.
 *
 * Copyright (c) 2023 Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Nodeloc\Referral;

use Flarum\Extend;
use Illuminate\Console\Scheduling\Event;
use Nodeloc\Referral\Api\Controller\CreateReferralRecordController;
use Nodeloc\Referral\Api\Controller\InviteRegisterController;
use Nodeloc\Referral\Api\Controller\ListReferralRecordController;
use Flarum\User\Event\Activated as UserActivated;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),

    new Extend\Locales(__DIR__ . '/locale'),

    // 前端 添加页面路由
    (new Extend\Frontend('forum'))
        ->route('/store', 'nodeloc.referral.store.index'),

    // 后端 添加接口
    (new Extend\Routes('api'))
        ->post('/store/referral', 'nodeloc.store.referral.create', CreateReferralRecordController::class)
        ->get('/store/referral/show', 'nodeloc.store.referral.show', ListReferralRecordController::class),
    (new Extend\Settings())
        ->serializeToForum('invite_code_price', 'nodeloc-flarum-ext-referral.price',)
        ->serializeToForum('invite_code_max_number', 'nodeloc-flarum-ext-referral.max_number',)
        ->serializeToForum('invite_code_expires', 'nodeloc-flarum-ext-referral.expires',)
        ->serializeToForum('invite_code_reward', 'nodeloc-flarum-ext-referral.reward',),
    // 权限 是不是可以不需要
    (new Extend\Policy())
        ->modelPolicy(ReferralRecord::class, Access\ReferralRecordPolicy::class),
    (new Extend\Console())
        ->command(Console\ExpireInviteCode::class)
        ->schedule('doorman:expire_invite_code', function (Event $event) {
            $event->daily();
        }),
    (new Extend\Event())
        ->listen(UserActivated::class, Listeners\RewardUser::class),
];
