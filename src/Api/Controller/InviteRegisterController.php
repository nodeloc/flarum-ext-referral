<?php

namespace Nodeloc\Referral\Api\Controller;


use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Illuminate\Support\Arr;

class InviteRegisterController implements RequestHandlerInterface
{
    protected $authResponse;
    protected $settings;
    protected $url;
    protected $userRepository;

    public function __construct(ResponseFactory $authResponse, SettingsRepositoryInterface $settings, UrlGenerator $url, UserRepository $userRepository)
    {
        $this->authResponse = $authResponse;
        $this->settings = $settings;
        $this->url = $url;
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request): ResponseInterface
    {
        try {

            $inviteKey = Arr::get($request->getQueryParams(), 'doorkey');
            // 返回注册页面
            return new HtmlResponse('Error: ' . $inviteKey, 500);
        } catch (Exception $e) {
            // 在异常情况下返回错误响应
            return new HtmlResponse('Error: ' . $e->getMessage(), 500);
        }
    }
}
