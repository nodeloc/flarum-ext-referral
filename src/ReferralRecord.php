<?php

namespace Nodeloc\Referral;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Foundation\EventGeneratorTrait;
use Flarum\User\User;
use FoF\Doorman\Doorkey;

class ReferralRecord extends AbstractModel
{
    protected $table = 'referral_records';
    public $timestamps = true;
    public $fillable = [
        'key_cost',
        'key_count',
        'registers',
        'actives',
        'is_expire',
        'user_id',
        'doorkey_id',
    ];

    public function doorKey()
    {
        return $this->belongsTo(DoorKey::class, 'doorkey_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
