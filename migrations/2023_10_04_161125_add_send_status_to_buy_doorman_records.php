<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Schema\Builder;


use Flarum\Database\Migration;

// HINT: you might want to use a `Flarum\Database\Migration` helper method for simplicity!
// See https://docs.flarum.org/extend/models.html#migrations to learn more about migrations.
return Migration::addColumns('buy_doorman_records', [
    'retry' => ['integer', 'default' => -1, 'comment' => '还可以重试的次数，-1=发送成功，0=表示一直未成功'],
    'error' => ['text', 'nullable' => true, 'comment' => '失败原因'],
]);
