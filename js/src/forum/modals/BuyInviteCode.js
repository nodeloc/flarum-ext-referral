import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';

export default class BuyInviteCode extends Modal {

  constructor() {
    super();
    this.invite_code_price = app.forum.attribute('invite_code_price');
    this.invite_code_reward = app.forum.attribute('invite_code_reward');
    this.invite_code_max_number = app.forum.attribute('invite_code_max_number');
    this.invite_code_expires = app.forum.attribute('invite_code_expires');
    this.key_count = Stream('0');
  }

  className() {
    return 'store-buy Modal--small';
  }

  title() {
    return app.translator.trans('nodeloc-referral.forum.purchase_invite_code');
  }

  onsubmit(e) {
    e.preventDefault();
    this.loading = true;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/store/referral',
      body: {
        key_count: this.key_count(),
      },
    }).then((result) => {
      this.loading = false;
      if (result.error) {
        app.alerts.show(
          {
            type: 'error',
          },
          result.error
        );
        return;
      }

      this.key_count('0');
      this.hide();
      m.route.set(m.route.get());
    });
  }

  content() {
    return (
      <div className="container buy-store-layer">
        <div className="Form">
          <div className="helpText">
            {app.translator.trans('nodeloc-referral.forum.purchase_help_tip', {
              invite_code_price: this.invite_code_price,
              invite_code_reward: this.invite_code_reward
            })}
          </div>
          <div className="Form-group">
            <label
              for="buy-store-to-mail">{app.translator.trans('nodeloc-referral.forum.purchase_invite_code', {invite_code_max_number: this.invite_code_max_number})}</label>
            <div
              className="helpText">{app.translator.trans('nodeloc-referral.forum.purchase_expire_tip', {invite_code_expires: this.invite_code_expires})} </div>
            <input
              required
              id="buy-store-to-mail"
              className="FormControl"
              type="number"
              bidi={this.key_count}
            />
          </div>
          <Button
            className="Button Button--primary"
            type="submit"
            loading={this.loading}
            onclick={(e) => this.onsubmit(e)}
          >
            购买
          </Button>
        </div>
      </div>
    );
  }
}
