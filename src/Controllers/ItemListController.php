<?php

namespace Nodeloc\Referral\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Nodeloc\Referral\FreecodeListItem;
use Nodeloc\Referral\FreecodeListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ItemListController extends AbstractListController
{
    public $serializer = FreecodeListItemSerializer::class;

    public $include = [
        'group',
        'members.groups',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $items = FreecodeListItem::query()->get();

        $items->load([
            'group',
        ]);

        return $items;
    }
}
