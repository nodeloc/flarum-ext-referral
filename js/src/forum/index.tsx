import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import LinkButton from 'flarum/common/components/LinkButton';
export {default as extend} from './extend';

extend(IndexPage.prototype, 'navItems', function (items) {
  items.add(
    'buy-doorman-store',
    <LinkButton href={app.route('imdong.buy-doorman.store.index')} icon="fas fa-store">
      小药店
    </LinkButton>,
    0
  );
});
