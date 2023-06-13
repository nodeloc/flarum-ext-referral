<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'buy_doorman_records',
    function (Blueprint $table) {
        $table->increments('id');

        $table->unsignedInteger('create_user_id')->comment('创建邀请码的用户ID');
        $table->double('money')->default(0)->comment('花了多少钱');
        $table->string('doorman_key')->comment('邀请码本码');
        $table->string('recipient')->nullable()->comment('收件人');

        // created_at & updated_at
        $table->timestamps();
    }
);

