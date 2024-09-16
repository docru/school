import{_ as V,m as k,a as N,v as e}from"./app-C2de6py2.js";import{C as b,S as D}from"./CourseSchedule-DkIeGLOz.js";const S={name:"DetailGroup",components:{CourseSchedule:b,StudyProgram:D},data(){return{tab:"lessons"}},computed:{...k("profile",["getProfile"]),...k("disciple",{group:"getGroup",courseSchoolDays:"getCourseSchoolDay",groupSchoolDays:"getGroupSchoolDay",getDisciples:"getDisciples",getTeachers:"getTeachers",attendances:"getAttendances"}),headers(){return[{title:"",align:"left",key:"order",width:"100px",fixed:!0,sortable:!1,divider:!0},{title:"Уроки",align:"left",key:"lessons",fixed:!0,sortable:!1,divider:!0},{title:"Посещение",align:"center",key:"attendance",width:"50px",fixed:!0,sortable:!1,divider:!0}]},teachers(){let t=[];for(const r in this.getTeachers)t.push({title:this.getTeachers[r].name??"-"});return t},disciples(){let t=[];for(const r in this.getDisciples)t.push({title:this.getDisciples[r].name??"-"});return t}},methods:{...N("disciple",["actRequestGroup"]),lessons(t){if(t){let r=[];for(const s in t)r.push(1+parseInt(s)+". "+t[s].name);return r.join("; ")}else return""},groupSchoolDay(t,r){var s,p;return(p=(s=this.groupSchoolDays)==null?void 0:s[t])==null?void 0:p[r]},attendance(t){return this.attendances.indexOf(t)>-1}},created(){this.actRequestGroup(this.$route.params.id)}},E={class:"tw-flex tw-justify-between"},T={class:"tw-flex tw-justify-between"},G={key:0,class:"tw-text-[12px]"},F={key:1,class:"tw-text-[12px]"},j=e.createElementVNode("h3",null,"Учителя",-1),I={key:1},L=e.createElementVNode("h3",null,"Ученики",-1),P={key:3};function q(t,r,s,p,i,n){const m=e.resolveComponent("v-tab"),y=e.resolveComponent("v-tabs"),v=e.resolveComponent("v-card-title"),g=e.resolveComponent("v-divider"),f=e.resolveComponent("router-link"),u=e.resolveComponent("v-chip"),w=e.resolveComponent("v-data-table"),h=e.resolveComponent("v-window-item"),_=e.resolveComponent("v-list"),C=e.resolveComponent("v-window"),x=e.resolveComponent("v-card-text"),B=e.resolveComponent("v-card");return e.openBlock(),e.createBlock(B,null,{default:e.withCtx(()=>[e.createVNode(v,null,{default:e.withCtx(()=>{var o,a,l,c;return[e.createElementVNode("div",E,[e.createElementVNode("div",null,'Группа "'+e.toDisplayString((a=(o=t.group)==null?void 0:o.group)==null?void 0:a.name)+'". Курс "'+e.toDisplayString((c=(l=t.group)==null?void 0:l.course)==null?void 0:c.name)+'"',1)]),e.createVNode(y,{modelValue:i.tab,"onUpdate:modelValue":r[0]||(r[0]=d=>i.tab=d),"bg-color":""},{default:e.withCtx(()=>[e.createVNode(m,{value:"lessons"},{default:e.withCtx(()=>[e.createTextVNode("Уроки")]),_:1}),t.getProfile.roles.includes("disciple")?e.createCommentVNode("",!0):(e.openBlock(),e.createBlock(m,{key:0,value:"group"},{default:e.withCtx(()=>[e.createTextVNode("Группа")]),_:1}))]),_:1},8,["modelValue"])]}),_:1}),e.createVNode(g),e.createVNode(x,null,{default:e.withCtx(()=>[e.createVNode(C,{modelValue:i.tab,"onUpdate:modelValue":r[1]||(r[1]=o=>i.tab=o)},{default:e.withCtx(()=>[e.createVNode(h,{value:"lessons"},{default:e.withCtx(()=>[e.createVNode(w,{mobile:!0,"fixed-header":"",density:"compact",items:t.courseSchoolDays,headers:n.headers,"hide-default-footer":!0,"disable-pagination":""},e.createSlots({"item.order":e.withCtx(({item:o})=>[e.createTextVNode(e.toDisplayString(o.order)+". "+e.toDisplayString(n.groupSchoolDay(o.id,"date")??"-"),1)]),"item.lessons":e.withCtx(({item:o})=>[(e.openBlock(!0),e.createElementBlock(e.Fragment,null,e.renderList(o.lessons??[],a=>(e.openBlock(),e.createBlock(u,{key:a.id,onClick:l=>t.$router.push({name:"DiscipleLesson",params:{groupId:t.group.group.id,lessonId:a.id}})},{default:e.withCtx(()=>[e.createTextVNode(e.toDisplayString(a.name),1)]),_:2},1032,["onClick"]))),128))]),"item.attendance":e.withCtx(({item:o})=>[n.groupSchoolDay(o.id,"date")?(e.openBlock(),e.createElementBlock(e.Fragment,{key:0},[n.attendance(o.id)?(e.openBlock(),e.createBlock(u,{key:0,color:"secondary",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Присутствовал ")]),_:1})):(e.openBlock(),e.createBlock(u,{key:1,color:"red",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Отсутствовал ")]),_:1}))],64)):e.createCommentVNode("",!0)]),_:2},[t.$vuetify.display.name==="sm"?{name:"headers",fn:e.withCtx(()=>[]),key:"0"}:void 0,t.$vuetify.display.name==="sm"?{name:"item",fn:e.withCtx(({item:o,columns:a})=>[t.$vuetify.display.name==="sm"?(e.openBlock(),e.createElementBlock("div",{key:0,class:e.normalizeClass(["tw-border tw-rounded tw-border-[rgba(221,218,218,0.49)] tw-mt-2 tw-p-2",{}])},[e.createElementVNode("div",T,[e.createTextVNode(e.toDisplayString(o.order)+". "+e.toDisplayString(n.groupSchoolDay(o.id,"date")??"-")+" ",1),n.groupSchoolDay(o.id,"date")?(e.openBlock(),e.createElementBlock(e.Fragment,{key:0},[n.attendance(o.id)?(e.openBlock(),e.createElementBlock("span",G," Присутствовал ")):(e.openBlock(),e.createElementBlock("span",F," Отсутствовал "))],64)):e.createCommentVNode("",!0)]),e.createTextVNode(" Уроки: "),(e.openBlock(!0),e.createElementBlock(e.Fragment,null,e.renderList(o.lessons,l=>{var c,d;return e.openBlock(),e.createElementBlock("div",{key:l.id,style:{color:"lightskyblue",margin:"5px 0"}},[e.createVNode(f,{style:{"border-bottom":"1px dashed lightskyblue"},to:{path:`/disciple/lesson/${(d=(c=t.group)==null?void 0:c.course)==null?void 0:d.id}/${l.id}`}},{default:e.withCtx(()=>[e.createTextVNode(e.toDisplayString(l.name),1)]),_:2},1032,["to"])])}),128))],2)):e.createCommentVNode("",!0)]),key:"1"}:void 0]),1032,["items","headers"])]),_:1}),e.createVNode(h,{value:"group"},{default:e.withCtx(()=>[j,n.teachers.length?(e.openBlock(),e.createBlock(_,{key:0,items:n.teachers,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",I,"Нет учителей")),L,n.disciples.length?(e.openBlock(),e.createBlock(_,{key:2,items:n.disciples,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",P,"Нет учеников"))]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})}const U=V(S,[["render",q]]);export{U as default};
