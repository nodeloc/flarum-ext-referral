<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

 namespace Nodeloc\Referral\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Flarum\User\User;
use Flarum\User\Event\Registered;
use FoF\Doorman\Doorkey;
use Nodeloc\Referral\ReferralRecord;
class UserRegisteredListener
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings,Dispatcher $events, )
    {
        $this->settings = $settings;
        $this->events = $events;
    }

    public function handle(Registered $event)
    {
        $inviteCode = $event->user->invite_code;
        $user = $event->user;
        $doorkey = Doorkey::where('key', $inviteCode)->first();
        if ($doorkey) {
            ReferralRecord::where('doorkey_id', $doorkey->id)->increment('registers');
            $referrerUserId = ReferralRecord::where('doorkey_id', $doorkey->id)->value('user_id');
            if ($referrerUserId) {
                $user->invite_user_id = $referrerUserId;
                $user->save();
            }
        }
    }
}
