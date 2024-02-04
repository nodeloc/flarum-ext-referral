<?php

namespace Nodeloc\Referral\Console;

use Flarum\Console\AbstractCommand;
use Flarum\Foundation\Paths;
use Flarum\User\User;
use Nodeloc\Referral\ReferralRecord;
use Nodeloc\Referral\ReferralRecordRepository;

class ExpireInviteCode extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('doorman:expire_invite_code')
            ->setDescription('Set expire invite code to invalid.');
    }


    /**
     *
     * 发送邮件
     *
     * @return void
     */
    private function expire_to_invalid()
    {
        // 获取过期天数设置
        $expireDays = (int) config('nodeloc-flarum-ext-referral.expireys', 1);

        // 计算过期日期
        $expiredAt = now()->subDays($expireDays);

        // 将过期的邀请码的 is_expire 设置为 0
        ReferralRecord::where('created_at', '<=', $expiredAt)
            ->update(['is_expire' => 1]);

        $this->info('Expired invitations updated successfully.');
    }

    protected function fire()
    {
        $this->expire_to_invalid();
    }
}
