import Model from 'flarum/common/Model';

export default class FreeCodeListItem extends Model {
  days = Model.attribute('days');
  amount = Model.attribute('amount');
  group = Model.hasOne('group');
}
