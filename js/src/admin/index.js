// @ts-ignore
import app from 'flarum/admin/app';
import FreeCodeListItem from "../common/models/FreeCodeListItem";
import FreeListSettingPage from "./FreeListSettingPage";
app.initializers.add('nodeloc/flarum-ext-referral', () => {
  app.store.models['freecode-list-items'] = FreeCodeListItem;
  app.extensionData
    .for('nodeloc-referral')
    .registerSetting({
      setting: 'nodeloc-flarum-ext-referral.price',
      label: app.translator.trans('nodeloc-referral.admin.settings.price'),
      type: 'number',
    })
    .registerSetting({
      setting: 'nodeloc-flarum-ext-referral.reward',
      label: app.translator.trans('nodeloc-referral.admin.settings.reward'),
      type: 'number',
    })
    .registerSetting({
      setting: 'nodeloc-flarum-ext-referral.max_number',
      label: app.translator.trans('nodeloc-referral.admin.settings.max_number'),
      type: 'number',
    })
    .registerSetting({
      setting: 'nodeloc-flarum-ext-referral.expires',
      label: app.translator.trans('nodeloc-referral.admin.settings.expires'),
      type: 'number',
    })
    .registerSetting({
      setting: 'nodeloc-flarum-ext-referral.key_count',
      label: app.translator.trans('nodeloc-referral.admin.settings.key_count'),
      type: 'number',
    })
    .registerPage(FreeListSettingPage)
    .registerPermission(
      {
        icon: 'fas fa-money-bill',
        label: app.translator.trans('nodeloc-referral.admin.permissions.referral_key'),
        permission: 'user.referral_key',
      },
      'moderate',
    );
});
