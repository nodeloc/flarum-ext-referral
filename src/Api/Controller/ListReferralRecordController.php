<?php

namespace Nodeloc\Referral\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Bus\Dispatcher;
use Nodeloc\Referral\ReferralRecord;
use Nodeloc\Referral\ReferralRecordRepository;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Nodeloc\Referral\Api\Serializer\ReferralRecordSerializer;

class ListReferralRecordController extends AbstractCreateController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = ReferralRecordSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    protected $repository;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus, ReferralRecordRepository $repository)
    {
        $this->bus = $bus;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        try {
            $records = ReferralRecord::with('doorkey')->where('user_id', $actor->id)->orderBy('created_at', 'desc')->get();
            return $records;
        } catch (\Exception|PermissionDeniedException $e) {
            header('Content-Type:  application/json; charset=UTF-8');
            die(json_encode([
                'error' => $e->getMessage(),
            ]));
        }
    }

}
