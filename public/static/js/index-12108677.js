/*!  build: Vue Shop Vite 
     copyright: https://vue-admin-beautiful.com/shop-vite  
     time: 2023-10-01 12:09:31 
 */
import{d as u,h as t,o as a,n,k as _,c,J as p,K as r,_ as m}from"./index-3cdf513a.js";const i=u({__name:"index",props:{modelValue:{},label:{},items:{}},emits:["update:modelValue"],setup(v){return(l,o)=>{const s=t("el-option"),d=t("el-select");return a(),n(d,{clearable:"","model-value":l.modelValue,placeholder:l.label,onChange:o[0]||(o[0]=e=>l.$emit("update:modelValue",e))},{default:_(()=>[(a(!0),c(p,null,r(l.items,e=>(a(),n(s,{key:e.value,label:e.label,value:e.value},null,8,["label","value"]))),128))]),_:1},8,["model-value","placeholder"])}}});const b=m(i,[["__scopeId","data-v-af6cdc9a"]]);export{b as default};
