<?php

namespace ImDong\BuyDoorman\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ErrorHandling\HandledError;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Bus\Dispatcher;
use ImDong\BuyDoorman\BuyDoormanRecordRepository;
use PHPUnit\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use ImDong\BuyDoorman\Api\Serializer\BuyDoormanRecordSerializer;

class CreateBuyDoormanRecordController extends AbstractCreateController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = BuyDoormanRecordSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    protected $repository;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus, BuyDoormanRecordRepository $repository)
    {
        $this->bus = $bus;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        // See https://docs.flarum.org/extend/api.html#api-endpoints for more information.

        $actor = RequestUtil::getActor($request);
        $data = $request->getParsedBody();

        // 调用邀请码创建
        try {
            return $this->repository->store($actor, $data);
        } catch (\Exception|PermissionDeniedException $e) {
            header('Content-Type:  application/json; charset=UTF-8');
            die(json_encode([
                'error' => $e->getMessage(),
            ]));
        }

    }
}
