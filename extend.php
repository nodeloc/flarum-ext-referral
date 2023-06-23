<?php

/*
 * This file is part of imdong/flarum-ext-buy-doorman.
 *
 * Copyright (c) 2023 ImDong.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace ImDong\BuyDoorman;

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    // 前端 添加页面路由
    (new Extend\Frontend('forum'))
        ->route('/store', 'imdong.buy-doorman.store.index'),

    // 后端 添加接口
    (new Extend\Routes('api'))
        ->post('/store/buy', 'imdong.buy-doorman.store.create', Api\Controller\CreateBuyDoormanRecordController::class),

    // 权限 是不是可以不需要
    (new Extend\Policy())
        ->modelPolicy(BuyDoormanRecord::class, Access\BuyDoormanRecordPolicy::class)

];
