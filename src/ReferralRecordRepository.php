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

class ReferralRecordRepository
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

    public function __construct(
        ReferralRecordValidator   $validator,
        SettingsRepositoryInterface $settings,
        TranslatorInterface         $translator,
        ExtensionManager            $extensions,
        UrlGenerator                $url
    )
    {
        $this->validator = $validator;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->extensions = $extensions;
        $this->url = $url;
        $this->key_price = (int)$this->settings->get('nodeloc-flarum-ext-referral.price', 1);;
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
     * 检查购买余额限制
     *
     * @param int $money
     * @return bool
     * @throws PermissionDeniedException
     */
    private function checkBuyMoney(int $money, int $key_count): bool
    {
        // 试图扣钱
        if (($money - $this->key_price * $key_count) < 0) {
            throw new PermissionDeniedException('能量不足!');
        }
        return true;
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

    /**
     * 创建邀请码
     *
     * @param User $actor 用户对象
     * @param array $data 请求参数
     * @return ReferralRecord
     * @throws ValidationException|PermissionDeniedException
     * @throws \Exception
     */
    public function store(User $actor, array $data): ReferralRecord
    {
        $this->validator->assertValid($data);

        $key_count = $data['key_count'];
        // 检查用户余额是否符合要求
        $money = $actor->getAttribute('money');
        $this->checkBuyMoney($money, $key_count);

        // 扣减金额
        $actor->money -= $this->key_price * $key_count;

        // 创建邀请码
        $key = $this->buildKey($actor->id);
        $doorkey = Doorkey::build($key, $this->defaultGroupId, $key_count, false);
        $record = new ReferralRecord([
            'user_id' => $actor->id,
            'key_cost' => $this->key_price * $key_count,
            'key_count' => $key_count,
            'is_expire' => 0,
        ]);
        // 手动关联 Doorkey 模型
        $doorkey->save();
        $record->doorKey()->associate($doorkey);
        $record->save();
        return $record;

    }
}
