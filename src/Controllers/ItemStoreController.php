<?php

namespace Nodeloc\Referral\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Group\Group;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Referral\FreecodeListItem;
use Nodeloc\Referral\FreecodeListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ItemStoreController extends AbstractCreateController
{
    public $serializer = FreecodeListItemSerializer::class;

    public $include = [
        'group',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $group = Group::query()->findOrFail(Arr::get($request->getParsedBody(), 'data.attributes.groupId'));

        $item = new FreecodeListItem();
        $item->group()->associate($group);
        $item->save();

        return $item;
    }
}
