import app from 'flarum/forum/app';
import RedirectToHomeAndOpenModalPage from './RedirectToHomeAndOpenModalPage';
import SignUpModal from 'flarum/forum/components/SignUpModal';
import Stream from 'flarum/common/utils/Stream';

export default class SignupPage extends RedirectToHomeAndOpenModalPage {
  oninit(vnode) {
    super.oninit(vnode);
    // 从 URL 中获取 doorkey 参数
    app.doorkey = vnode.attrs.doorkey || '';
  }

  createModal() {
    if (!app.session.user) {
      return SignUpModal;
    }
  }
}
