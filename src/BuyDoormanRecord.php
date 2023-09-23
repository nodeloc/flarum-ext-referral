<?php

namespace ImDong\BuyDoorman;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;

class BuyDoormanRecord extends AbstractModel
{
    // See https://docs.flarum.org/extend/models.html#backend-models for more information.

    protected $table = 'buy_doorman_records';

    public $timestamps = true;

    public $fillable = [
        'create_user_id',
        'money',
        'doorman_key',
        'recipient',
    ];
}
