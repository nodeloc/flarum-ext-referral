<?php

namespace Nodeloc\Referral;

use Flarum\Foundation\AbstractValidator;

class ReferralRecordValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        // See https://laravel.com/docs/8.x/validation#available-validation-rules for more information.
        'key_count' => 'required',
    ];
}
