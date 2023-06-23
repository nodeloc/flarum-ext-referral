<?php

namespace ImDong\BuyDoorman\Command;

use Flarum\User\User;

class CreateBuyDoormanRecord
{
    /**
     * @var \Flarum\User\User
     */
    public $actor;

    /**
     * @var array
     */
    public $data;

    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }
}
