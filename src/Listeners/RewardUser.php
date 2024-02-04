<?php

namespace Nodeloc\Referral\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Doorman\Doorkey;
use Illuminate\Contracts\Events\Dispatcher;
use Nodeloc\Referral\ReferralRecord;
use Flarum\User\Event\Activated as UserActivated;
use Illuminate\Support\Facades\Log;

class RewardUser
{
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->reword_money = (int)$this->settings->get('nodeloc-flarum-ext-referral.reward', 1);
    }
    public function subscribe(Dispatcher $events)
    {
        $events->listen(UserActivated::class, [$this, 'handle']);
    }

    public function handle(UserActivated $event)
    {
        $inviteCode = $event->user->invite_code;
        // 使用 invite_code 查找 doorkey
        $doorkey = Doorkey::where('key', $inviteCode)->first();

        if ($doorkey) {
            // 查找关联的 user_id
            $referrerUserId = ReferralRecord::where('doorkey_id', $doorkey->id)->value('user_id');

            if ($referrerUserId) {
                // 更新邀请者的 money 字段
                $referrerUser = User::find($referrerUserId);

                if ($referrerUser) {
                    $referrerUser->money += $this->reword_money;
                    $referrerUser->save();
                }
                // 更新 ReferralRecord 中的 actives 值
                ReferralRecord::where('doorkey_id', $doorkey->id)->increment('actives');
            }
        }
    }
}
