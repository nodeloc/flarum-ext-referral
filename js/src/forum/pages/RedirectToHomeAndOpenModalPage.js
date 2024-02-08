import app from 'flarum/forum/app';
import Page from 'flarum/common/components/Page';

export default class RedirectToHomeAndOpenModalPage extends Page {
  oninit(vnode) {
    super.oninit(vnode);

    m.route.set('/');

    setTimeout(() => app.modal.show(this.createModal()), 1500);
  }

  createModal() {
    return null;
  }

  view() {
    return m('div');
  }
}
