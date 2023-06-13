import app from 'flarum/forum/app';
import IndexPage from 'flarum/forum/components/IndexPage';
import listItems from 'flarum/common/helpers/listItems';
import type Mithril from 'mithril';
import Page, { IPageAttrs } from 'flarum/common/components/Page';
import Button from 'flarum/common/components/Button';
import SendDoormanEmail from "../modals/SendDoormanEmail";

export interface IIndexPageAttrs extends IPageAttrs {}
export default class StoreIndex<CustomAttrs extends IIndexPageAttrs = IIndexPageAttrs> extends IndexPage {
  oncreate(vnode: Mithril.VnodeDOM<CustomAttrs, this>) {
    super.oncreate(vnode);

    app.setTitle("小药店");
    app.setTitleCount(0);
  }

  view() {
    return (
      <div className="IndexPage">
        <div className="container">
          <div className="sideNavContainer">
            <nav className="IndexPage-nav sideNav">
              <ul>{listItems(this.sidebarItems().toArray())}</ul>
            </nav>
            <div className="IndexPage-results sideNavOffset">
              <h2 class="BadgeOverviewTitle">小药店</h2>
              <Button className={"Button Button--primary"} onclick={() => {
                app.modal.show(SendDoormanEmail)
              }}>购买邀请码</Button>
            </div>
          </div>
        </div>
      </div>
    )
  }


}
