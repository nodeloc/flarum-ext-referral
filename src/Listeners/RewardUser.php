<?php

namespace Nodeloc\Referral\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Doorman\Doorkey;
use Illuminate\Contracts\Events\Dispatcher;
use Nodeloc\Referral\ReferralRecord;
use Flarum\User\Event\Activated as UserActivated;
use Illuminate\Support\Facades\Log;
use Flarum\Locale\Translator;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class RewardUser
{
    protected $settings;
    protected $events;
    protected $reword_money;
    /**
     * @var int 注册用户默认用户组
     */
    protected $defaultGroupId = 3;
    /**
     * @var int 免费送几个key
     */
    private $free_key;
    protected $translator;
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, Translator $translator)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->translator = $translator;
        $this->reword_money = (int)$this->settings->get('nodeloc-flarum-ext-referral.reward', 1);
    }
    public function subscribe(Dispatcher $events)
    {
        $events->listen(UserActivated::class, [$this, 'handle']);
    }
    private function buildKey(int $user_id): string
    {
        $rand_str = sprintf(
            '%s%s%04d%s',
            bin2hex(random_bytes(1)), // 前缀随机数
            base_convert(time() - 1645539742, 10, 16), // 当前时间 - 2022-02-22 22:22:22
            base_convert($user_id, 10, 16),    // 用户 ID 5 位数应该够用了
            bin2hex(random_bytes(2)), // 后随机数
        );
        return strtoupper(base_convert($rand_str, 16, 36));
    }

    /**
     * 创建邀请码
     *
     * @param User $actor 用户对象
     * @param array $data 请求参数
     * @return ReferralRecord
     * @throws ValidationException|PermissionDeniedException
     * @throws \Exception
     */
    public function make_free_key(User $actor)
    {

        $key_count =  (int)$this->settings->get('nodeloc-flarum-ext-referral.key_count', 3);
        if($key_count>0){
            // 创建邀请码
            $key = $this->buildKey($actor->id);
            $doorkey = Doorkey::build($key, $this->defaultGroupId, $key_count, false);
            $record = new ReferralRecord([
                'user_id' => $actor->id,
                'key_cost' => 0,
                'key_count' => $key_count,
                'is_expire' => 0,
            ]);
            // 手动关联 Doorkey 模型
            $doorkey->save();
            $record->doorKey()->associate($doorkey);
            $actor->save();
            $record->save();
        }
        return;
    }
    public function handle(UserActivated $event)
    {
        $inviteCode = $event->user->invite_code;
        // 赠送邀请码
        $this->make_free_key($event->user);
        if($this->reword_money>0){
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
                        $source = 'REFERRAL';
                        $sourceDesc = $this->translator->trans("antoinefr-money.forum.history.referral");
                        $this->events->dispatch(new MoneyHistoryEvent($referrerUser, $this->reword_money, $source, $sourceDesc));
                    }
                    // 更新 ReferralRecord 中的 actives 值
                    ReferralRecord::where('doorkey_id', $doorkey->id)->increment('actives');
                }
            }
        }
    }
}
