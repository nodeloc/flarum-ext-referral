<?php

namespace Nodeloc\Referral\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Referral\FreecodeListItem;
use Psr\Http\Message\ServerRequestInterface;

class ItemDeleteController extends AbstractDeleteController
{
    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        $item = FreecodeListItem::query()->findOrFail($id);

        $item->delete();
    }
}
