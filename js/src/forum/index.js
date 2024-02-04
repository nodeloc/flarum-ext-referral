import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import LinkButton from 'flarum/common/components/LinkButton';
export {default as extend} from './extend';

extend(IndexPage.prototype, 'navItems', function (items) {
  items.add(
    'referral-store',
    <LinkButton href={app.route('nodeloc.referral.store.index')} icon="fas fa-share-alt">
        {app.translator.trans('nodeloc-referral.forum.referral')}
    </LinkButton>,
    0
  );
});
