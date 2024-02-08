(()=>{var e={n:t=>{var o=t&&t.__esModule?()=>t.default:()=>t;return e.d(o,{a:o}),o},d:(t,o)=>{for(var r in o)e.o(o,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:o[r]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};(()=>{"use strict";e.r(t),e.d(t,{extend:()=>q});const o=flarum.core.compat["forum/app"];var r=e.n(o);const n=flarum.core.compat["common/extend"],a=flarum.core.compat["forum/components/IndexPage"];var i=e.n(a);const c=flarum.core.compat["common/components/LinkButton"];var s=e.n(c);function u(e,t){return u=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e},u(e,t)}function l(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,u(e,t)}const d=flarum.core.compat["common/components/Page"];var p=function(e){function t(){return e.apply(this,arguments)||this}l(t,e);var o=t.prototype;return o.oninit=function(t){var o=this;e.prototype.oninit.call(this,t),m.route.set("/"),setTimeout((function(){return r().modal.show(o.createModal())}),1500)},o.createModal=function(){return null},o.view=function(){return m("div")},t}(e.n(d)());const f=flarum.core.compat["forum/components/SignUpModal"];var v=e.n(f);const _=flarum.core.compat["common/utils/Stream"];var y=e.n(_),h=function(e){function t(){return e.apply(this,arguments)||this}l(t,e);var o=t.prototype;return o.oninit=function(t){e.prototype.oninit.call(this,t),r().doorkey=t.attrs.doorkey||""},o.createModal=function(){if(!r().session.user)return v()},t}(p);const b=flarum.core.compat["common/extenders"];var x=e.n(b);const g=flarum.core.compat["components/IndexPage"];var k=e.n(g);const N=flarum.core.compat["common/helpers/listItems"];var w=e.n(N);const P=flarum.core.compat["common/components/Button"];var C=e.n(P);const O=flarum.core.compat["common/components/Modal"];var S=function(e){function t(){var t;return(t=e.call(this)||this).invite_code_price=r().forum.attribute("invite_code_price"),t.invite_code_reward=r().forum.attribute("invite_code_reward"),t.invite_code_max_number=r().forum.attribute("invite_code_max_number"),t.invite_code_expires=r().forum.attribute("invite_code_expires"),t.key_count=y()("0"),t}l(t,e);var o=t.prototype;return o.className=function(){return"store-buy Modal--small"},o.title=function(){return r().translator.trans("nodeloc-referral.forum.purchase_invite_code")},o.onsubmit=function(e){var t=this;e.preventDefault(),this.loading=!0,r().request({method:"POST",url:r().forum.attribute("apiUrl")+"/store/referral",body:{key_count:this.key_count()}}).then((function(e){t.loading=!1,e.error?r().alerts.show({type:"error"},e.error):(t.key_count("0"),t.hide(),m.route.set(m.route.get()))}))},o.content=function(){var e=this;return m("div",{className:"container buy-store-layer"},m("div",{className:"Form"},m("div",{className:"helpText"},r().translator.trans("nodeloc-referral.forum.purchase_help_tip",{invite_code_price:this.invite_code_price,invite_code_reward:this.invite_code_reward})),m("div",{className:"Form-group"},m("label",{for:"buy-store-to-mail"},r().translator.trans("nodeloc-referral.forum.purchase_invite_code",{invite_code_max_number:this.invite_code_max_number})),m("div",{className:"helpText"},r().translator.trans("nodeloc-referral.forum.purchase_expire_tip",{invite_code_expires:this.invite_code_expires})," "),m("input",{required:!0,id:"buy-store-to-mail",className:"FormControl",type:"number",bidi:this.key_count})),m(C(),{className:"Button Button--primary",type:"submit",loading:this.loading,onclick:function(t){return e.onsubmit(t)}},"购买")))},t}(e.n(O)());const I=flarum.core.compat["common/components/LoadingIndicator"];var M=e.n(I);const T=flarum.core.compat["common/Component"];var j=e.n(T);const B=flarum.core.compat["common/components/Alert"];var F=e.n(B),U=function(e){function t(){return e.apply(this,arguments)||this}l(t,e);var o=t.prototype;return o.oninit=function(t){var o=this;e.prototype.oninit.call(this,t),r().setTitle(r().translator.trans("nodeloc-referral.forum.referral")),r().setTitleCount(0),r().forum.attribute("invite_code_price"),r().forum.attribute("invite_code_max_number"),r().forum.attribute("invite_code_expires"),r().request({method:"GET",url:r().forum.attribute("apiUrl")+"/store/referral/show"}).then((function(e){e.data&&(o.records=e,m.redraw())}))},o.view=function(){return m("div",{className:"IndexPage"},k().prototype.hero(),m("div",{className:"container"},m("div",{className:"sideNavContainer"},m("nav",{className:"IndexPage-nav sideNav"},m("ul",null,w()(k().prototype.sidebarItems().toArray()))),m("div",{class:"StoreIndex"},m("div",{class:"container"},m("div",{class:"sideNavContainer"},m("div",{class:"StoreIndex-results sideNavOffset"},m(C(),{class:"Button Button--primary",onclick:function(){r().modal.show(S)}},r().translator.trans("nodeloc-referral.forum.purchase_invite_code")),m("div",{class:"StoreIndex-Body"},this.records?this.recordsContent():m(M(),null)))))))))},o.recordsContent=function(){return console.log("records:",this.records.data.attributes),this.records.data.attributes&&0!==this.records.data.attributes.length?m("div",{className:"ReferralHistoryContainer"},m("ul",null,this.records.data.attributes.map((function(e){return m("li",{key:e.id,className:"copyable-item",onclick:function(){return t=e.doorkey.key,(o=document.createElement("textarea")).value=r().forum.attribute("baseUrl")+"/signup/"+t,document.body.appendChild(o),o.select(),document.execCommand("copy"),document.body.removeChild(o),void r().alerts.show(F(),{type:"success"},"邀请码已复制到剪贴板");var t,o}},m("p",null," ",r().translator.trans("nodeloc-referral.forum.create_time"),": ",(t=e.created_at,new Date(t).toLocaleString())),m("p",null,r().translator.trans("nodeloc-referral.forum.invite_code"),": ",m("span",{className:"copyable"},e.doorkey.key)),m("p",null," ",r().translator.trans("nodeloc-referral.forum.count"),": ",e.key_count),m("p",null," ",r().translator.trans("nodeloc-referral.forum.cost"),": ",e.key_cost," 能量"),m("p",null," ",r().translator.trans("nodeloc-referral.forum.actives"),": ",e.actives),m("p",null," ",r().translator.trans("nodeloc-referral.forum.is_expire"),": ",e.is_expire?"是":"否"));var t})))):""},t}(j());const q=[(new(x().Routes)).add("nodeloc.referral.store.index","/store",U)];r().routes.nodeloc_signup={path:"/signup",component:h},r().routes.nodeloc_signup_invite={path:"/signup/:doorkey",component:h},r().initializers.add("nodeloc-referral",(function(){(0,n.extend)(r().routes,"nodeloc_signup",{path:"/signup",component:h}),(0,n.extend)(r().routes,"nodeloc_signup_invite",{path:"/signup/:doorkey",component:h}),(0,n.extend)(v().prototype,"fields",(function(e){var t=r().forum.data.attributes["fof-doorman.allowPublic"]?r().translator.trans("fof-doorman.forum.sign_up.doorman_placeholder_optional"):r().translator.trans("fof-doorman.forum.sign_up.doorman_placeholder");this.doorkey=y()(r().doorkey)||y()(""),e.add("doorkey",m("div",{className:"Form-group"},m("input",{className:"FormControl",name:"fof-doorkey",type:"text",placeholder:t,bidi:this.doorkey,disabled:this.loading})))})),(0,n.extend)(v().prototype,"submitData",(function(e){var t=e;return t["fof-doorkey"]=this.doorkey,t}))})),(0,n.extend)(i().prototype,"navItems",(function(e){e.add("referral-store",m(s(),{href:r().route("nodeloc.referral.store.index"),icon:"fas fa-share-alt"},r().translator.trans("nodeloc-referral.forum.referral")),0)}))})(),module.exports=t})();
//# sourceMappingURL=forum.js.map