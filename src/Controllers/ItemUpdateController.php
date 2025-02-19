<?php

namespace Nodeloc\Referral\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Formatter\Formatter;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Referral\FreecodeListItem;
use Nodeloc\Referral\FreecodeListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ItemUpdateController extends AbstractShowController
{
    public $serializer = FreecodeListItemSerializer::class;

    public $include = [
        'group',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $item = FreecodeListItem::query()->findOrFail($id);
        if (Arr::exists($attributes, 'amount')) {
            $item->amount = Arr::get($attributes, 'amount');
        }
        if (Arr::exists($attributes, 'days')) {
            $item->days = Arr::get($attributes, 'days');
        }


        $item->save();

        return $item;
    }
}
