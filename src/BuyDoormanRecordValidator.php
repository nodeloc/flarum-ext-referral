<?php

namespace ImDong\BuyDoorman;

use Flarum\Foundation\AbstractValidator;

class BuyDoormanRecordValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        // See https://laravel.com/docs/8.x/validation#available-validation-rules for more information.
        'email' => 'required|email',
        'message' => 'string',
    ];
}
