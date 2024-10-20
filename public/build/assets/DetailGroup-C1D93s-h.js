import{_ as V,m as k,a as b,v as e}from"./app-D4g4NRDO.js";import{C as N,S as D}from"./CourseSchedule-CvpDRe0S.js";const S={name:"DetailGroup",components:{CourseSchedule:N,StudyProgram:D},data(){return{tab:"lessons"}},computed:{...k("profile",["getProfile"]),...k("disciple",{group:"getGroup",courseSchoolDays:"getCourseSchoolDay",groupSchoolDays:"getGroupSchoolDay",getDisciples:"getDisciples",getTeachers:"getTeachers",attendances:"getAttendances"}),headers(){return[{title:"",align:"left",key:"order",width:"100px",fixed:!0,sortable:!1,divider:!0},{title:"Уроки",align:"left",key:"lessons",fixed:!0,sortable:!1,divider:!0},{title:"Посещение",align:"center",key:"attendance",width:"50px",fixed:!0,sortable:!1,divider:!0}]},teachers(){let t=[];for(const n in this.getTeachers)t.push({title:this.getTeachers[n].name??"-"});return t},disciples(){let t=[];for(const n in this.getDisciples)t.push({title:this.getDisciples[n].name??"-"});return t}},methods:{...b("disciple",["actRequestGroup"]),lessons(t){if(t){let n=[];for(const a in t)n.push(1+parseInt(a)+". "+t[a].name);return n.join("; ")}else return""},groupSchoolDay(t,n){var a,p;return(p=(a=this.groupSchoolDays)==null?void 0:a[t])==null?void 0:p[n]},attendance(t){return this.attendances.indexOf(t)>-1}},created(){this.actRequestGroup(this.$route.params.id)}},E={class:"tw-flex tw-justify-between"},T={class:"tw-text-[12px]"},G={class:"tw-flex tw-justify-between"},F={key:0,class:"tw-text-[12px]"},j={key:1,class:"tw-text-[12px]"},I=["onClick"],L={class:"tw-text-[lightskyblue]",style:{"border-bottom":"1px dashed lightskyblue",cursor:"pointer"}},P=e.createElementVNode("h3",null,"Учителя",-1),q={key:1},A=e.createElementVNode("h3",null,"Ученики",-1),R={key:3};function U(t,n,a,p,i,r){const u=e.resolveComponent("v-tab"),y=e.resolveComponent("v-tabs"),g=e.resolveComponent("v-card-title"),v=e.resolveComponent("v-divider"),f=e.resolveComponent("router-link"),m=e.resolveComponent("v-chip"),w=e.resolveComponent("v-data-table"),h=e.resolveComponent("v-window-item"),_=e.resolveComponent("v-list"),x=e.resolveComponent("v-window"),C=e.resolveComponent("v-card-text"),B=e.resolveComponent("v-card");return e.openBlock(),e.createBlock(B,null,{default:e.withCtx(()=>[e.createVNode(g,null,{default:e.withCtx(()=>{var o,s,l,c;return[e.createElementVNode("div",E,[e.createElementVNode("div",null,'Группа "'+e.toDisplayString((s=(o=t.group)==null?void 0:o.group)==null?void 0:s.name)+'". Курс "'+e.toDisplayString((c=(l=t.group)==null?void 0:l.course)==null?void 0:c.name)+'"',1)]),e.createVNode(y,{modelValue:i.tab,"onUpdate:modelValue":n[0]||(n[0]=d=>i.tab=d),"bg-color":""},{default:e.withCtx(()=>[e.createVNode(u,{value:"lessons"},{default:e.withCtx(()=>[e.createTextVNode("Уроки")]),_:1}),t.getProfile.roles.includes("disciple")?e.createCommentVNode("",!0):(e.openBlock(),e.createBlock(u,{key:0,value:"group"},{default:e.withCtx(()=>[e.createTextVNode("Группа")]),_:1}))]),_:1},8,["modelValue"])]}),_:1}),e.createVNode(v),e.createVNode(C,null,{default:e.withCtx(()=>[e.createVNode(x,{modelValue:i.tab,"onUpdate:modelValue":n[1]||(n[1]=o=>i.tab=o)},{default:e.withCtx(()=>[e.createVNode(h,{value:"lessons"},{default:e.withCtx(()=>[e.createVNode(w,{mobile:!0,"fixed-header":"",density:"compact",items:t.courseSchoolDays,headers:r.headers,"hide-default-footer":!0,"disable-pagination":""},e.createSlots({"item.order":e.withCtx(({item:o})=>[e.createElementVNode("span",T,e.toDisplayString(r.groupSchoolDay(o.id,"date")??"-"),1)]),"item.lessons":e.withCtx(({item:o})=>[(e.openBlock(!0),e.createElementBlock(e.Fragment,null,e.renderList(o.lessons??[],(s,l)=>(e.openBlock(),e.createElementBlock("div",{class:"tw-my-1",key:s.id,onClick:c=>t.$router.push({name:"DiscipleLesson",params:{groupId:t.group.group.id,lessonId:s.id}})},[e.createElementVNode("span",L,e.toDisplayString(l+1)+". "+e.toDisplayString(s.name),1)],8,I))),128))]),"item.attendance":e.withCtx(({item:o})=>[r.groupSchoolDay(o.id,"date")?(e.openBlock(),e.createElementBlock(e.Fragment,{key:0},[r.attendance(o.id)?(e.openBlock(),e.createBlock(m,{key:0,color:"secondary",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Присутствовал ")]),_:1})):(e.openBlock(),e.createBlock(m,{key:1,color:"red",density:"compact"},{default:e.withCtx(()=>[e.createTextVNode(" Отсутствовал ")]),_:1}))],64)):e.createCommentVNode("",!0)]),_:2},[t.$vuetify.display.name==="sm"?{name:"headers",fn:e.withCtx(()=>[]),key:"0"}:void 0,t.$vuetify.display.name==="sm"?{name:"item",fn:e.withCtx(({item:o,columns:s})=>[t.$vuetify.display.name==="sm"?(e.openBlock(),e.createElementBlock("div",{key:0,class:e.normalizeClass(["tw-border tw-rounded tw-border-[rgba(221,218,218,0.49)] tw-mt-2 tw-p-2",{}])},[e.createElementVNode("div",G,[e.createTextVNode(e.toDisplayString(o.order)+". "+e.toDisplayString(r.groupSchoolDay(o.id,"date")??"-")+" ",1),r.groupSchoolDay(o.id,"date")?(e.openBlock(),e.createElementBlock(e.Fragment,{key:0},[r.attendance(o.id)?(e.openBlock(),e.createElementBlock("span",F," Присутствовал ")):(e.openBlock(),e.createElementBlock("span",j," Отсутствовал "))],64)):e.createCommentVNode("",!0)]),e.createTextVNode(" Уроки: "),(e.openBlock(!0),e.createElementBlock(e.Fragment,null,e.renderList(o.lessons,l=>{var c,d;return e.openBlock(),e.createElementBlock("div",{key:l.id,style:{color:"lightskyblue",margin:"5px 0"}},[e.createVNode(f,{style:{"border-bottom":"1px dashed lightskyblue"},to:{path:`/disciple/lesson/${(d=(c=t.group)==null?void 0:c.course)==null?void 0:d.id}/${l.id}`}},{default:e.withCtx(()=>[e.createTextVNode(e.toDisplayString(l.name),1)]),_:2},1032,["to"])])}),128))],2)):e.createCommentVNode("",!0)]),key:"1"}:void 0]),1032,["items","headers"])]),_:1}),e.createVNode(h,{value:"group"},{default:e.withCtx(()=>[P,r.teachers.length?(e.openBlock(),e.createBlock(_,{key:0,items:r.teachers,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",q,"Нет учителей")),A,r.disciples.length?(e.openBlock(),e.createBlock(_,{key:2,items:r.disciples,density:"compact"},null,8,["items"])):(e.openBlock(),e.createElementBlock("div",R,"Нет учеников"))]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})}const H=V(S,[["render",U]]);export{H as default};