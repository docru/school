import{_ as w,a as V,m as D,v as e}from"./app-DAbPShih.js";import{C as N,S as B}from"./CourseSchedule-B8cn6Rxq.js";const b={name:"DetailGroup",components:{CourseSchedule:N,StudyProgram:B},data(){return{tab:"lessons"}},computed:{...V("disciple",{group:"getGroup",courseSchoolDays:"getCourseSchoolDay",groupSchoolDays:"getGroupSchoolDay",getDisciples:"getDisciples",getTeachers:"getTeachers",attendances:"getAttendances"}),headers(){return[{title:"",align:"left",key:"order",width:"100px",fixed:!0,sortable:!1,divider:!0},{title:"Уроки",align:"left",key:"lessons",fixed:!0,sortable:!1,divider:!0},{title:"Посещение",align:"center",key:"attendance",width:"50px",fixed:!0,sortable:!1,divider:!0}]},teachers(){let t=[];for(const o in this.getTeachers)t.push({title:this.getTeachers[o].name??"-"});return t},disciples(){let t=[];for(const o in this.getDisciples)t.push({title:this.getDisciples[o].name??"-"});return t}},methods:{...D("disciple",["actRequestGroup"]),lessons(t){if(t){let o=[];for(const a in t)o.push(1+parseInt(a)+". "+t[a].name);return o.join("; ")}else return""},groupSchoolDay(t,o){var a,c;return(c=(a=this.groupSchoolDays)==null?void 0:a[t])==null?void 0:c[o]},attendance(t){return this.attendances.indexOf(t)>-1}},created(){this.actRequestGroup(this.$route.params.id)}},S={class:"tw-flex tw-justify-between"},T=e.createElementVNode("h3",null,"Учителя",-1),E={key:1},G=e.createElementVNode("h3",null,"Ученики",-1),I={key:3};function j(t,o,a,c,l,n){const p=e.resolveComponent("v-tab"),_=e.resolveComponent("v-tabs"),v=e.resolveComponent("v-card-title"),g=e.resolveComponent("v-divider"),i=e.resolveComponent("v-chip"),f=e.resolveComponent("v-data-table"),u=e.resolveComponent("v-window-item"),m=e.resolveComponent("v-list"),k=e.resolveComponent("v-window"),y=e.resolveComponent("v-card-text"),C=e.resolveComponent("v-card");return e.openBlock(),e.createBlock(C,null,{default:e.withCtx(()=>[e.createVNode(v,null,{default:e.withCtx(()=>{var r,s,d,h;return[e.createElementVNode("div",S,[e.createElementVNode("div",null,'Группа "'+e.toDisplayString((s=(r=t.group)==null?void 0:r.group)==null?void 0:s.name)+'". Курс "'+e.toDisplayString((h=(d=t.group)==null?void 0:d.course)==null?void 0:h.name)+'"',1)]),e.createVNode(_,{modelValue:l.tab,"onUpdate:modelValue":o[0]||(o[0]=x=>l.tab=x),"bg-color":""},{default:e.withCtx(()=>[e.createVNode(p,{value:"lessons"},{default:e.withCtx(()=>[e.createTextVNode("Уроки")]),_:1}),e.createVNode(p,{value:"group"},{default:e.withCtx(()=>[e.createTextVNode("Группа")]),_:1})]),_:1},8,["modelValue"])]}),_:1}),e.createVNode(g),e.createVNode(y,null,{default:e.withCtx(()=>[e.createTextVNode(e.toDisplayString(t.attendances)+" ",1),e.createVNode(k,{modelValue:l.tab,"onUpdate:modelValue":o[1]||(o[1]=r=>l.tab=r)},{default:e.withCtx(()=>[e.createVNode(u,{value:"lessons"},{default:e.withCtx(()=>[e.createVNode(f,{class:"table-sh",height:"800","fixed-header":"",density:"compact",items:t.courseSchoolDays,headers:n.headers,"hide-default-footer":!0,"disable-pagination":""},{"item.order":e.withCtx(({item:r})=>[e.createTextVNode(e.toDisplayString(r.order)+". "+e.toDisplayString(n.groupSchoolDay(r.id,"date")??"-"),1)]),"item.lessons":e.withCtx(({item:r})=>[(e.openBlock(!0),e.createElementBlock(e.Fragment,null,e.renderList(r.lessons??[],s=>(e.openBlock(),e.createBlock(i,{key:s.id,onClick:d=>t.$router.push({name:"DiscipleLesson",params:{groupId:t.group.group.id,lessonId:s.id}})},{default:e.withCtx(()=>[e.createTextVNode(e.toDisplayString(s.name),1)]),_:2},1032,["onClick"]))),128))]),"item.attendance":e.withCtx(({item:r})=>[n.groupSchoolDay(r.id,"date")?(e.openBlock(),e.createElementBlock(e.Fragment,{key:0},[n.attendance(r.id)?(e.openBlock(),e.createBlock(i,{key:0,color:"secondary",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Присутствовал ")]),_:1})):(e.openBlock(),e.createBlock(i,{key:1,color:"red",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Отсутствовал ")]),_:1}))],64)):e.createCommentVNode("",!0)]),_:1},8,["items","headers"])]),_:1}),e.createVNode(u,{value:"group"},{default:e.withCtx(()=>[T,n.teachers.length?(e.openBlock(),e.createBlock(m,{key:0,items:n.teachers,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",E,"Нет учителей")),G,n.disciples.length?(e.openBlock(),e.createBlock(m,{key:2,items:n.disciples,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",I,"Нет учеников"))]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})}const F=w(b,[["render",j]]);export{F as default};
