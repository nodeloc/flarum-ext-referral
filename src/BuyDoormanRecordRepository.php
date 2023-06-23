<?php

namespace ImDong\BuyDoorman;

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

class BuyDoormanRecordRepository
{

    /**
     * @var BuyDoormanRecordValidator
     */
    protected $validator;

    /**
     * @var Mailer
     */
    protected $mailer;

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
     * @var int 邀请码价格
     */
    protected $doormanPrice = 100;

    /**
     * @var int[] 购买余额限制 (购买前大于该余额的，购买后也不应该小于该值)
     */
    protected $moneyBalanceLimits = [
//        50, // 软盘
//        100, // U 盘
        300, // 硬盘
        1200, // 阵列
    ];

    /**
     * @var int 注册用户默认用户组
     */
    protected $defaultGroupId = 3;

    public function __construct(
        BuyDoormanRecordValidator   $validator,
        Mailer                      $mailer,
        SettingsRepositoryInterface $settings,
        TranslatorInterface         $translator,
        ExtensionManager            $extensions,
        UrlGenerator                $url
    )
    {
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->extensions = $extensions;
        $this->url = $url;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return BuyDoormanRecord::query();
    }

    /**
     * @param int $id
     * @param User $actor
     * @return BuyDoormanRecord
     */
    public function findOrFail($id, User $actor = null): BuyDoormanRecord
    {
        return BuyDoormanRecord::findOrFail($id);
    }

    /**
     * 检查购买余额限制
     *
     * @param int $money
     * @return bool
     * @throws PermissionDeniedException
     */
    private function checkBuyMoney(int $money): bool
    {
        // 先排序
        sort($this->moneyBalanceLimits);

        // 但也不能低于最小值
        if ($money < $this->moneyBalanceLimits[0]) {
            throw new PermissionDeniedException('不满足购买要求（药丸大于300）');
        }


        foreach ($this->moneyBalanceLimits as $balanceLimit) {
            if ($money > $balanceLimit && ($money - $this->doormanPrice) < $balanceLimit) {
                throw new PermissionDeniedException('购买后余额不足');
            }
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
        return base_convert($rand_str, 16, 36);
    }

    /**
     * 发送邀请码到用户邮箱
     *
     * @param string $email
     * @param string $doorkey
     * @return void
     */
    private function sendInvites(string $email, string $doorkey)
    {
        $title = $this->settings->get('forum_title');
        $subject = $this->settings->get('forum_title') . ' - ' . $this->translator->trans('fof-doorman.forum.email.subject');
        $body = $this->translator->trans('fof-doorman.forum.email.body', [
            '{forum}' => $title,
            '{url}' => $this->extensions->isEnabled('zerosonesfun-direct-links') ? $this->url->to('forum')->route('direct-links-signup') : $this->url->to('forum')->base(),
            '{code}' => $doorkey,
        ]);

        $this->mailer->raw(
            $body,
            function (Message $message) use ($subject, $email) {
                $message->to($email)->subject($subject);
            }
        );
    }

    /**
     * 创建邀请码
     *
     * @param User $actor 用户对象
     * @param array $data 请求参数
     * @return BuyDoormanRecord
     * @throws ValidationException|PermissionDeniedException
     * @throws \Exception
     */
    public function store(User $actor, array $data): BuyDoormanRecord
    {
        $this->validator->assertValid($data);

        // 检查用户余额是否符合要求
        $money = $actor->getAttribute('money');
        $this->checkBuyMoney($money);

        // 先扣减用户余额，然后再创建邀请码(懒得折腾事务了，上文件锁)
        $lock_file = sprintf('%s/flarum-money-%s.lock', sys_get_temp_dir(), $actor->id);
        $fd = fopen($lock_file, 'w+');
        if (!flock($fd, LOCK_EX)) {
            throw new \Exception();
        }

        // 扣减金额
        $actor->money -= $this->doormanPrice;

        // 创建邀请码
        $key = $this->buildKey($actor->id);
        $doorkey = Doorkey::build($key, $this->defaultGroupId, 1, false);

        // 购买记录 （以后查档用）
        $record = new BuyDoormanRecord([
            'create_user_id' => $actor->id,
            'money' => $this->doormanPrice,
            'doorman_key' => $key,
            'recipient' => $data['email'],
        ]);

        // 保存就是了
        $doorkey->save();
        $actor->save();
        $record->save();

        // 发送邀请码到收件人邮箱
        $this->sendInvites($data['email'], $key);

        // 还是返回购买记录比较好
        return $record;
    }
}
