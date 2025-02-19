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
    const noInviteText = app.translator.trans('nodeloc-referral.forum.sign_up.no_invite_text');
    const getInviteLinkText = app.translator.trans('nodeloc-referral.forum.sign_up.get_invite_link_text');

    fields.add(
      'get-invite-code-text',
      m(
        '.Form-group',
        m(
          '.get-invite-code-text Alert',
          noInviteText,
          m('a', { href: '/p/5-get-invite-code' }, getInviteLinkText)
        )
      )
    );
  });

  extend(SignUpModal.prototype, 'submitData', function (data) {
    const newData = data;
    const doorkeyValue = this.doorkey() !== undefined ? this.doorkey() : $('input[name="fof-doorkey"]').val();
    newData['fof-doorkey'] = doorkeyValue;
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
