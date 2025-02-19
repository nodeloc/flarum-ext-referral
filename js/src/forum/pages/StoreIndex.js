import IndexPage from 'flarum/components/IndexPage';
import app from 'flarum/forum/app';
import listItems from 'flarum/common/helpers/listItems';
import Button from 'flarum/common/components/Button';
import BuyInviteCode from "../modals/BuyInviteCode";
import FreeInviteCode from "../modals/FreeInviteCode";
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Component from 'flarum/common/Component';
import Alert from 'flarum/common/components/Alert';

export default class StoreIndex extends Component {
  oninit(vnode) {
    super.oninit(vnode);

    app.setTitle(app.translator.trans('nodeloc-referral.forum.referral'));
    app.setTitleCount(0);
    const invite_code_price = app.forum.attribute("invite_code_price");
    const invite_code_max_number = app.forum.attribute("invite_code_max_number");
    const invite_code_expires = app.forum.attribute("invite_code_expires");
    this.loadData();
  }
  loadData() {
    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/store/referral/show',
    }).then(response => {
      if (response.data) {
        this.records = response;
        m.redraw();
      }
    });
  }
  view() {
    return (
      <div className="IndexPage">
        {IndexPage.prototype.hero()}
        <div className="container">
          <div className="sideNavContainer">
            <nav className="IndexPage-nav sideNav">
              <ul>{listItems(IndexPage.prototype.sidebarItems().toArray())}</ul>
            </nav>
            <div class="StoreIndex">
              <div class="container">
                <div class="sideNavContainer">
                  <div class="StoreIndex-results sideNavOffset">
                    <Button
                      class="Button Button--primary"
                      onclick={() => {
                        app.modal.show(BuyInviteCode);
                      }}
                    >
                      {app.translator.trans('nodeloc-referral.forum.purchase_invite_code')}
                    </Button>

                    <Button
                      style="margin-left: 10px;"
                      class="Button Button--primary"
                      onclick={() => {
                        app.modal.show(FreeInviteCode);
                      }}
                    >
                      {app.translator.trans('nodeloc-referral.forum.free_invite_code')}
                    </Button>
                    <div class="StoreIndex-Body">
                      {this.records ? this.recordsContent() :
                        <LoadingIndicator/>}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  recordsContent() {
    const formatDate = (timestamp) => {
      const date = new Date(timestamp);
      return date.toLocaleString(); // 使用本地格式显示日期和时间
    };
    const copyToClipboard = (text) => {
      const tempInput = document.createElement('textarea');
      tempInput.value = app.forum.attribute('baseUrl') + '/signup/'+text;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      // 显示复制成功的提示消息
      app.alerts.show(Alert, {type: 'success'}, "邀请码已复制到剪贴板");
    };


    if (!this.records.data.attributes || this.records.data.attributes.length === 0) {
      return "";
    }
    return (
      <div className="ReferralHistoryContainer">
        <ul>
          {this.records.data.attributes.map(record => (
            <li key={record.id}
                className={record.is_expire ? 'expired' : 'copyable-item'}
                onclick={record.doorkey ? () => copyToClipboard(record.doorkey.key) : null}
                >
              <p> {app.translator.trans('nodeloc-referral.forum.create_time')}: {formatDate(record.created_at)}</p>
              <p>
                {app.translator.trans('nodeloc-referral.forum.invite_code')}: <span className={record.is_expire ? 'expired' : 'copyable'}>
                  {record.doorkey ? (record.doorkey.key ? record.doorkey.key : "已过期") : "已过期"}
                </span>
              </p>
              <p> {app.translator.trans('nodeloc-referral.forum.count')}: {parseInt(record.key_count) - parseInt(record.registers)}</p>
              <p> {app.translator.trans('nodeloc-referral.forum.cost')}: {record.key_cost} 能量</p>
              <p> {app.translator.trans('nodeloc-referral.forum.registers')}: {record.registers}</p>
              <p> {app.translator.trans('nodeloc-referral.forum.actives')}: {record.actives}</p>
            </li>
          ))}
        </ul>
      </div>
    );
  }
}
