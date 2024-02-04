<?php

use Illuminate\Database\Schema\Blueprint;
use Flarum\Database\Migration;

return Migration::createTable(
    'referral_records',
    function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->comment('创建邀请码的用户ID');
        $table->integer('doorkey_id')->comment('doorkey主键');
        $table->double('key_cost')->default(0)->comment('花了多少钱');
        $table->integer('key_count')->comment('购买了几个码子');
        $table->integer('actives')->comment('过期时间');
        $table->tinyInteger('is_expire')->nullable()->comment('是否失效');

        $table->timestamps();
    }
);
