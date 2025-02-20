import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
import ItemList from 'flarum/common/utils/ItemList';
import UserCard from 'flarum/forum/components/UserCard';
import icon from 'flarum/common/helpers/icon';

import type Mithril from 'mithril';

export default function addInviterToUsers() {
  User.prototype.inviter = Model.attribute<string>('inviter');

  extend(UserCard.prototype, 'infoItems', function (items: ItemList<Mithril.Children>) {
    const user = this.attrs.user;
    const inviter = user.inviter();

    // 如果邀请者存在，生成一个可以点击的链接
    if (inviter) {
      items.add(
        'inviter',
        <span className="UserCard-inviter">
        {icon('fa-solid fa-people-arrows')}
          <a href={`/u/${inviter}`}>
          {app.translator.trans('nodeloc-referral.forum.inviter', {
            inviter: inviter,
          })}
        </a>
      </span>,
        55
      );
    }
  });
}
