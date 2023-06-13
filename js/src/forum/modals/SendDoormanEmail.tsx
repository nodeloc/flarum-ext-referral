import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from "flarum/common/components/Button";

export default class SendDoormanEmail extends Modal {
  className() {
    return 'Modal--small';
  }

  title() {
    return "购买邀请码";
  }

  storeBuy(id: string) {
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/store/buy',
    }).then(result => {
      console.log(result)
    });
  }

  content() {
    return (
      <div className="container buy-store-layer">
        <div className="Form">
          <div class="Form-group">
            <label for="buy-store-to-mail">受邀人邮箱</label>
            <div class="helpText">邀请码购买成功后，将通过邮件发送到受邀人邮箱中。</div>
            <input id="buy-store-to-mail" class="FormControl" type="text"/>
          </div>
          <Button className={"Button Button--primary"} onclick={() => {
            this.storeBuy('1')
          }}>购买 & 发送</Button>
        </div>
      </div>
    );
  }
}
