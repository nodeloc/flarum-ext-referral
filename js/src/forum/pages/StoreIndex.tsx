import app from 'flarum/forum/app';
import IndexPage from 'flarum/forum/components/IndexPage';
import listItems from 'flarum/common/helpers/listItems';

export default class StoreIndex extends IndexPage {
  oncreate(vnode) {
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
              Hello World!
            </div>
          </div>
        </div>
      </div>
    )
  }


}
