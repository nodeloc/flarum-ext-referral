<?php

namespace Nodeloc\Referral\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ErrorHandling\HandledError;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Bus\Dispatcher;
use Nodeloc\Referral\FreeReferralRecordRepository;
use PHPUnit\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Nodeloc\Referral\Api\Serializer\ReferralRecordSerializer;

class CreateFreeReferralRecordController extends AbstractCreateController
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
    public function __construct(Dispatcher $bus, FreeReferralRecordRepository $repository)
    {
        $this->bus = $bus;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        try {
            return $this->repository->store($actor);
        } catch (ValidationException $e) {
            throw $e;
        } catch (PermissionDeniedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['error' => $e->getMessage()], 400)
            );
        }


    }
}
