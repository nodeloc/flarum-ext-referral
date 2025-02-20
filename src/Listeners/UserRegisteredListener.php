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
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Flarum\Foundation\ValidationException;
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
        // 使用 invite_code 查找 doorkey
        $doorkey = Doorkey::where('key', $inviteCode)->first();
        if ($doorkey) {
            // 更新 ReferralRecord 中的 actives 值
            ReferralRecord::where('doorkey_id', $doorkey->id)->increment('registers');
            // 查找关联的 user_id
            $referrerUserId = ReferralRecord::where('doorkey_id', $doorkey->id)->value('user_id');

            if ($referrerUserId) {
                $referrerUser = User::find($referrerUserId);
                if ($referrerUser) {
                    $referrerUser->invite_user_id = $event->user->id;
                    $referrerUser->save();
                }
            }
        }

    }
}
