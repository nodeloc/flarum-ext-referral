import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import humanTime from 'flarum/common/utils/humanTime';
import dayjs from 'dayjs';

export default class FreeInviteCode extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

    this.loading = true;
    this.lastRecord = null; // 最后一次免费领取记录
    this.freecodeList = []; // 用户组邀请码信息
    this.days = 0;
    this.amount = 0;
    this.canClaim = false; // 是否可以领取
    this.remainingTime = null; // 冷却时间

    // 请求 API
    const lastFreeCodeRequest = app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/store/referral/last-free-code',
    });

    const freecodeListRequest = app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/freecode-list',
    });

    Promise.all([lastFreeCodeRequest, freecodeListRequest])
      .then(([lastFreeCodeResponse, freecodeListResponse]) => {
        this.lastRecord = lastFreeCodeResponse.data?.attributes || {};
        this.freecodeList = freecodeListResponse.data || [];

        const userGroups = app.session.user?.groups() || [];
        const userGroupPermissions = userGroups.map(group => ({
          id: group.id(),
          readPermission: group.attribute('readPermission') || 0, // 读取扩展字段
        })).filter(Boolean);

        const highestPermissionGroup = userGroupPermissions.reduce(
          (max, group) => (group.readPermission > max.readPermission ? group : max),
          { id: null, readPermission: -1 }
        );

        // **匹配 freecode-list 获取 days 和 amount**
        const matchedFreeCode = this.freecodeList.find(
          item => item.relationships?.group?.data?.id === highestPermissionGroup.id
        );

        if (matchedFreeCode) {
          this.days = matchedFreeCode.attributes.days;
          this.amount = matchedFreeCode.attributes.amount;
        }

        // **判断是否可以领取**
        if (this.lastRecord.id) {
          const lastClaimTime = dayjs(this.lastRecord.created_at);
          const now = dayjs();
          const diffHours = now.diff(lastClaimTime, 'hour'); // 计算小时差
          const cooldownHours = this.days * 24;
          this.remainingTime = Math.max(cooldownHours - diffHours, 0);
          this.canClaim = diffHours >= cooldownHours;
        } else {
          this.canClaim = true;
        }

        this.loading = false;
        m.redraw();
      })
      .catch(error => {
        console.error('Error loading data:', error);
        this.loading = false;
      });
  }

  onsubmit(e) {
    e.preventDefault();
    if (!this.canClaim) return;

    this.loading = true;
    m.redraw();

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/store/free',
    })
      .then((result) => {
        this.loading = false;
        if (result.error) {
          app.alerts.show({ type: 'error' }, result.error);
          return;
        }
        this.hide();
        m.route.set(m.route.get());
      })
      .catch((error) => {
        this.loading = false;
        app.alerts.show({ type: 'error' }, app.translator.trans('nodeloc-referral.forum.claim_failed'));
        m.redraw();
      });
  }

  content() {
    return (
      <div className="container buy-store-layer">
        <div className="Form">
          <div className="helpText">
            {app.translator.trans('nodeloc-referral.forum.free_help_tip', {
              days: this.days,
              amount: this.amount
            })}
          </div>
          <div className="Form-group">
            {this.remainingTime > 0 && (
              <div className="helpText">
                {app.translator.trans('nodeloc-referral.forum.next_available', {
                  hours: this.remainingTime
                })}
              </div>
            )}
          </div>
          <Button
            className="Button Button--primary"
            type="submit"
            loading={this.loading}
            disabled={!this.canClaim}
            onclick={(e) => this.onsubmit(e)}
          >
            {this.canClaim
              ? app.translator.trans('nodeloc-referral.forum.claim_now')
              : app.translator.trans('nodeloc-referral.forum.wait_to_claim')}
          </Button>
        </div>
      </div>
    );
  }
}
