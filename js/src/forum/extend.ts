import Extend from 'flarum/common/extenders';
import StoreIndex from "./pages/StoreIndex";

export default [
  new Extend.Routes()
    .add('imdong.buy-doorman.store.index', '/store', StoreIndex),
];
