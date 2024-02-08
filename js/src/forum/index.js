import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import LinkButton from 'flarum/common/components/LinkButton';
import SignupPage from "./pages/SignupPage";
export {default as extend} from './extend';
import SignUpModal from 'flarum/forum/components/SignUpModal';
import Stream from 'flarum/common/utils/Stream';
// 定义路由
app.routes['nodeloc_signup'] = { path: '/signup', component: SignupPage };
app.routes['nodeloc_signup_invite'] = { path: '/signup/:doorkey', component: SignupPage };

app.initializers.add('nodeloc-referral', () => {
// 扩展路由
  extend(app.routes, 'nodeloc_signup', {
    path: '/signup',
    component: SignupPage
  });

  extend(app.routes, 'nodeloc_signup_invite', {
    path: '/signup/:doorkey',
    component: SignupPage
  });
  extend(SignUpModal.prototype, 'fields', function (fields) {
    const isOptional = app.forum.data.attributes['fof-doorman.allowPublic'];
    const placeholder = isOptional
      ? app.translator.trans('fof-doorman.forum.sign_up.doorman_placeholder_optional')
      : app.translator.trans('fof-doorman.forum.sign_up.doorman_placeholder');
    this.doorkey = Stream(app.doorkey) || Stream('');
    fields.add(
      'doorkey',
      <div className="Form-group">
        <input className="FormControl" name="fof-doorkey" type="text" placeholder={placeholder} bidi={this.doorkey} disabled={this.loading} />
      </div>
    );
  });

  extend(SignUpModal.prototype, 'submitData', function (data) {
    const newData = data;
    newData['fof-doorkey'] = this.doorkey;
    return newData;
  });
});
extend(IndexPage.prototype, 'navItems', function (items) {
  items.add(
    'referral-store',
    <LinkButton href={app.route('nodeloc.referral.store.index')} icon="fas fa-share-alt">
        {app.translator.trans('nodeloc-referral.forum.referral')}
    </LinkButton>,
    0
  );

});
