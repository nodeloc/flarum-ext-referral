<?php

namespace ImDong\BuyDoorman\Console;

use Flarum\Console\AbstractCommand;
use Flarum\Foundation\Paths;
use Flarum\User\User;
use ImDong\BuyDoorman\BuyDoormanRecord;
use ImDong\BuyDoorman\BuyDoormanRecordRepository;

class SendDoormanMail extends AbstractCommand
{
    /**
     * @var string 锁文件位置
     */
    private $lock_file = '';

    /**
     * @var int 发送邮件休息间隔时间 单位秒
     */
    private $send_sleep = 5;

    /**
     * @var int 发送多少个停止
     */
    private $send_max = 2;

    protected function configure()
    {
        $this
            ->setName('doorman:sendMail')
            ->setDescription('发送邀请码邮件');
    }

    /**
     * @return string 获取锁定文件位置
     */
    private function getLockFile(): string
    {
        if (empty($this->lock_file)) {
            $this->lock_file = resolve(Paths::class)->storage . '/cache/send-doorman-mail.lock';
        }

        return $this->lock_file;
    }

    protected function fire()
    {
        // 首先有一个文件锁
        $fd = fopen($this->getLockFile(), 'w+');
        if (!flock($fd, LOCK_EX | LOCK_NB)) {
            $this->error("is running...exit");

        }

        // 发送邮件
        $this->sendMail();

        // 解锁
        flock($fd, LOCK_UN);
    }

    /**
     *
     * 发送邮件
     *
     * @return void
     */
    private function sendMail()
    {
        // 获取对象
        $obj = resolve(BuyDoormanRecordRepository::class);

        // 从数据库获取待发送记录
        $user_table = (new User)->getTable();
        $records = BuyDoormanRecord::query()
            ->leftJoin($user_table, 'create_user_id', '=', "$user_table.id")
            ->select([
                "$user_table.username",// "$user_table.nickname",
                (new BuyDoormanRecord)->getTable() .'.id', 'doorman_key', 'recipient', 'message', 'retry'
            ])
            ->where('retry', '>', 0)
            ->orderBy('created_at')
            ->limit($this->send_max)
            ->get();

        /**
         * 发送
         */
        foreach ($records as $record) {
            $nickname = $record->nickname ?? $record->username;
            // 发送记录
            $this->info(sprintf('send %s to %s from %s message %s', $record->doorman_key, $record->recipient, $nickname, $record->message));

            try {
                // 发送邮件
                $obj->sendInvites(
                    $record->recipient,
                    $record->doorman_key,
                    $record->message,
                    $nickname
                );

                // 更新为已经发送
                $update = [
                    'retry' => -1
                ];
//                $record->retry = -1;
            } catch (\Exception $e) {
                $this->error(trim($e->getMessage()));

                // 更新失败次数
                $update = [
                    'retry' => $record->retry - 1,
                    'error' => trim($e->getMessage())
                ];
//                $record->retry = $record->retry - 1;
//                $record->error = trim($e->getMessage());

            }

            // 更新到数据库
            $res = BuyDoormanRecord::query()
                ->where('doorman_key', $record->doorman_key)
                ->update($update);

            // 休息几秒
            sleep($this->send_sleep);
        }
    }
}
