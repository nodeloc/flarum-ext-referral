<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('freecode_list', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->unique();
            $table->integer('days');
            $table->integer('amount');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('freecode_list');
    },
];
