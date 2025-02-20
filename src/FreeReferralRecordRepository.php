<?php

namespace Nodeloc\Referral;

use Flarum\Foundation\Paths;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use FoF\Doorman\Doorkey;
use Illuminate\Contracts\Mail\Mailer;
use Flarum\Settings\SettingsRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Extension\ExtensionManager;
use Flarum\Http\UrlGenerator;
use Illuminate\Mail\Message;
use Illuminate\Contracts\Events\Dispatcher;
use Carbon\Carbon;

// 获取当前时间
class FreeReferralRecordRepository
{

    /**
     * @var ReferralRecordValidator
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var int 注册用户默认用户组
     */
    protected $defaultGroupId = 3;
    private $key_price;
    protected $events;

    public function __construct(
        ReferralRecordValidator   $validator,
        SettingsRepositoryInterface $settings,
        TranslatorInterface         $translator,
        ExtensionManager            $extensions,
        UrlGenerator                $url,
        Dispatcher $events,

    )
    {
        $this->validator = $validator;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->extensions = $extensions;
        $this->url = $url;
        $this->key_price = (int)$this->settings->get('nodeloc-flarum-ext-referral.price', 1);
        $this->events = $events;

    }

    /**
     * @return Builder
     */
    public function query()
    {
        return ReferralRecord::query();
    }

    /**
     * @param int $id
     * @param User $actor
     * @return ReferralRecord
     */
    public function findOrFail($id, User $actor = null): ReferralRecord
    {
        return ReferralRecord::findOrFail($id);
    }


    /**
     * 生成一个 key
     *
     * @param int $user_id
     * @return string
     * @throws \Exception
     */
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

    public function store(User $actor): ReferralRecord
    {
        // 获取用户的所有用户组
        $userGroups = $actor->groups()->get();

        if ($userGroups->isEmpty()) {
            throw new PermissionDeniedException();
        }

        // 获取用户组中 read_permission 最高的那个
        $highestPermissionGroup = $userGroups->sortByDesc('read_permission')->first();

        if (!$highestPermissionGroup) {
            throw new PermissionDeniedException();
        }

        // 获取 freecode 配置
        $freecodeList = FreecodeListItem::query()->get();
        // 查找对应用户组的 freecode 配置
        $matchedFreeCode = collect($freecodeList)->firstWhere('group_id', $highestPermissionGroup->id);

        if (!$matchedFreeCode) {
            throw new PermissionDeniedException(); // 没有权限
        }

        // **查询 key_cost = 0 的最后一条领取记录**
        $lastRecord = ReferralRecord::where('user_id', $actor->id)
            ->where('key_cost', 0) // 只查询免费的
            ->orderBy('created_at', 'desc')
            ->first();
        $now = Carbon::now();

        // 判断冷却时间是否已过
        if ($lastRecord) {
            $cooldownEndTime = $lastRecord->created_at->addDays($matchedFreeCode['days']);
            if ($now->lessThan($cooldownEndTime)) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    response()->json(['error' => $this->translator->trans('nodeloc-referral.forum.wait_to_claim', [
                        'hours' => $cooldownEndTime->diffInHours($now)
                    ])], 400)
                );
            }
        }

        // 获取可生成的邀请码数量
        $key_count = $matchedFreeCode['amount'];

        // 生成邀请码
        $key = $this->buildKey($actor->id);
        $doorkey = Doorkey::build($key, $this->defaultGroupId, $key_count, false);

        $record = new ReferralRecord([
            'user_id' => $actor->id,
            'key_cost' => 0,
            'key_count' => $key_count,
            'is_expire' => 0,
        ]);

        // 关联 Doorkey 并保存
        $doorkey->save();
        $record->doorKey()->associate($doorkey);
        $record->save();

        return $record;
    }


}
