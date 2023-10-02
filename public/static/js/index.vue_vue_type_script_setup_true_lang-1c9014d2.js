/*!  build: Vue Shop Vite 
     copyright: https://vue-admin-beautiful.com/shop-vite  
     time: 2023-10-01 12:09:31 
 */
import{d as u,h as r,o as a,n as t,k as n,c as p,J as m,K as i,q as _,t as c}from"./index-3cdf513a.js";const g=u({__name:"index",props:{modelValue:{},label:{},items:{}},emits:["update:modelValue"],setup(b){return(l,o)=>{const s=r("el-radio"),d=r("el-radio-group");return a(),t(d,{"model-value":l.modelValue,onChange:o[0]||(o[0]=e=>l.$emit("update:modelValue",e))},{default:n(()=>[(a(!0),p(m,null,i(l.items,e=>(a(),t(s,{key:e.value,border:"",label:e.value,placeholder:e.label},{default:n(()=>[_(c(e.label),1)]),_:2},1032,["label","placeholder"]))),128))]),_:1},8,["model-value"])}}});export{g as _};
