import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import FreeListConfigurator from './FreeListConfigurator';

export default class FreeListSettingPage extends ExtensionPage {
  content() {
    return m('.ExtensionPage-settings',
      m('.container', [
        super.content(), // 继承默认的 registerSetting 内容
        m('.Form-group', m(FreeListConfigurator)), // 你自定义的组件
      ])
    );
  }
}
