import Extend from 'flarum/common/extenders';
import StoreIndex from "./pages/StoreIndex";

export default [
  new Extend.Routes()
    .add('nodeloc.referral.store.index', '/store', StoreIndex),
];
