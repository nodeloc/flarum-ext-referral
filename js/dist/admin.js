(()=>{var e={n:t=>{var r=t&&t.__esModule?()=>t.default:()=>t;return e.d(r,{a:r}),r},d:(t,r)=>{for(var n in r)e.o(r,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:r[n]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};(()=>{"use strict";e.r(t);const r=flarum.core.compat["admin/app"];var n=e.n(r);function o(e,t){return o=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e},o(e,t)}function a(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,o(e,t)}const i=flarum.core.compat["common/Model"];var s=e.n(i),l=function(e){function t(){for(var t,r=arguments.length,n=new Array(r),o=0;o<r;o++)n[o]=arguments[o];return(t=e.call.apply(e,[this].concat(n))||this).days=s().attribute("days"),t.amount=s().attribute("amount"),t.group=s().hasOne("group"),t}return a(t,e),t}(s());const u=flarum.core.compat["admin/components/ExtensionPage"];var c=e.n(u);const p=flarum.core.compat["common/components/Button"];var d=e.n(p);const f=flarum.core.compat["common/components/Dropdown"];var g=e.n(f);const y=flarum.core.compat["common/components/LoadingIndicator"];var b=e.n(y);const h=flarum.core.compat["common/models/Group"];var v=e.n(h);const _=flarum.core.compat["common/helpers/icon"];var x=e.n(_),P=function(){function e(){}var t=e.prototype;return t.oninit=function(){var e=this;this.items=null,app.request({method:"GET",url:app.forum.attribute("apiUrl")+"/freecode-list"}).then((function(t){e.items=app.store.pushPayload(t),m.redraw()}))},t.view=function(){var e=this,t=null===this.items?[]:this.items.map((function(e){return e.group().id()}));return m("table.FreeListTable",m("tbody",[null===this.items?m("tr",m("td",b().component())):this.items.map((function(t,r){return m("tr",[m("td",t.group().namePlural()),m("td",m("input.FormControl",{type:"number",min:0,step:"any",onchange:function(e){t.save({days:e.target.value}).then((function(){m.redraw()}))},value:t.days()})),m("td",m("input.FormControl",{type:"number",min:0,step:"any",onchange:function(e){t.save({amount:e.target.value}).then((function(){m.redraw()}))},value:t.amount()})),m("td",m("button.Button.Button--danger",{onclick:function(n){n.preventDefault(),t.delete().then((function(){e.items.splice(r,1),m.redraw()}))}},x()("fas fa-times")))])})),m("tr",m("td",{colspan:5},g().component({label:app.translator.trans("nodeloc-referral.admin.items.add"),buttonClassName:"Button"},app.store.all("groups").filter((function(e){return e.id()!==v().MEMBER_ID&&e.id()!==v().GUEST_ID&&-1===t.indexOf(e.id())})).map((function(t){return d().component({onclick:function(){app.request({method:"POST",url:app.forum.attribute("apiUrl")+"/freecode-list-items",body:{data:{attributes:{groupId:t.id()}}}}).then((function(t){e.items.push(app.store.pushPayload(t)),m.redraw()}))}},t.namePlural())})))))]))},e}(),O=function(e){function t(){return e.apply(this,arguments)||this}return a(t,e),t.prototype.content=function(){return m(".ExtensionPage-settings",m(".container",[e.prototype.content.call(this),m(".Form-group",m(P))]))},t}(c());n().initializers.add("nodeloc/flarum-ext-referral",(function(){n().store.models["freecode-list-items"]=l,n().extensionData.for("nodeloc-referral").registerSetting({setting:"nodeloc-flarum-ext-referral.price",label:n().translator.trans("nodeloc-referral.admin.settings.price"),type:"number"}).registerSetting({setting:"nodeloc-flarum-ext-referral.reward",label:n().translator.trans("nodeloc-referral.admin.settings.reward"),type:"number"}).registerSetting({setting:"nodeloc-flarum-ext-referral.max_number",label:n().translator.trans("nodeloc-referral.admin.settings.max_number"),type:"number"}).registerSetting({setting:"nodeloc-flarum-ext-referral.expires",label:n().translator.trans("nodeloc-referral.admin.settings.expires"),type:"number"}).registerSetting({setting:"nodeloc-flarum-ext-referral.key_count",label:n().translator.trans("nodeloc-referral.admin.settings.key_count"),type:"number"}).registerPage(O).registerPermission({icon:"fas fa-money-bill",label:n().translator.trans("nodeloc-referral.admin.permissions.referral_key"),permission:"user.referral_key"},"moderate")}))})(),module.exports=t})();
//# sourceMappingURL=admin.js.map