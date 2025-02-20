<?php

/*
 * This file is part of flarumite/simple-discussion-views.
 *
 * Copyright (c) 2020 Flarumite.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('users', 'invite_user_id')) {
            $schema->table('users', function (Blueprint $table) {
                $table->integer('invite_user_id')->default(NULL)->unsigned();
            });
        }
    },
    'down' => function (Builder $schema) {
        // no.
    },
];
