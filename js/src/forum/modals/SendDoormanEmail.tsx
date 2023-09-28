import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from "flarum/common/components/Button";
import Stream from 'flarum/common/utils/Stream';

export default class SendDoormanEmail extends Modal {
  private email: Stream;
  private message: Stream;

  constructor() {
    super();

    this.email = Stream();
    this.message = Stream();
  }

  money() {
    // 判断时间
    let start = new Date('2023-09-29 00:00:00'),
        end = new Date('2023-10-03 23:59:59'),
        now = new Date();

    return now > start && start <=end ? 1 : 100
  }

  className() {
    return 'store-buy Modal--small';
  }

  title() {
    return "购买邀请码";
  }

  content() {
    return (
      <div className="container buy-store-layer">
        <div className="Form">
          <div class="helpText">将花费 { this.money()} 药丸，购买一个注册邀请码，并发送到“受邀人邮箱”中。</div>
          <div class="Form-group">
            <label for="buy-store-to-mail">受邀人邮箱</label>
            <div class="helpText">邀请码购买成功后，将通过邮件发送到受邀人邮箱中。</div>
            <input required id="buy-store-to-mail" class="FormControl" type="email" bidi={this.email}/>
          </div>
          <div class="Form-group">
            <label htmlFor="buy-store-to-mail">留言</label>
            <div class="helpText">留言将与邀请码邮件一同送与收件人(可空)。</div>
            <input id="buy-store-to-message" class="FormControl" type="text" bidi={this.message}/>
          </div>
          {Button.component(
            {
              className: 'Button Button--primary',
              type: 'submit',
              loading: this.loading,
            },
            "购买 & 发送"
          )}
        </div>
      </div>
    );
  }

  onsubmit(e: Event) {
    e.preventDefault();
    this.loading = true;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/store/buy-doorman',
      body: {
        email: this.email(),
        message: this.message(),
      }
    }).then(result => {
      console.log('result', result)

      // 关闭加载中状态
      this.loading = false

      // 是有否错误
      if (result.error) {
        app.alerts.show({
          type: "error",
        }, result.error);
        return;
      }

      app.alerts.show({
        type: "success",
      }, '邀请邮件已加入发送队列，若超过一小时未收到请联系管理员。');

      // 清空邮箱
      this.email('')
      this.message('')

      // 关闭购买框
      this.hide()

      // 刷新用户余额(爱咋咋地 红就红吧 能跑就行)
      let money = app.session.user.attribute('money');
      console.log('money', money, result);
      app.session.user.pushAttributes({
        money: money - result.data.attributes.use_money,
      })
    });
  }

}
