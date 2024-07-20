import{_ as V,a as w,m as N,v as e}from"./app-DPmebXKC.js";import{S as x,C as y}from"./CourseSchedule-H8fWM9_K.js";const I={name:"CourseDetail",components:{StudyProgram:x,CourseSchedule:y},data(){return{tab:"studyProgram",timerSaveId:null,lastChangeDataTimerId:!1,lastChangeDataHash:!1,lastChangeDataTm:0}},computed:{...w("methodologist",["getCourse","getStudyProgram","isSaved","getHashParams"]),courseId(){return this.$route.params.idCourse},cntLessons(){let t=0;for(const o in this.getStudyProgram)t=t+this.getStudyProgram[o].lessons.length;return t},cntHours(){let t=0;for(const o in this.getStudyProgram)for(const r in this.getStudyProgram[o].lessons){let l=this.getStudyProgram[o].lessons[r].hours;t=t+l}return t}},methods:{...N("methodologist",["actReqwestCourse","actSaveCourse"]),async save(){await this.actSaveCourse()},autoSaveStart(){let t=750;this.lastChangeDataTimerId=setInterval(async()=>{let o=this.getHashParams;o!==this.lastChangeDataHash&&(this.lastChangeDataTm=performance.now(),this.lastChangeDataHash=o)},t/2),this.timerSaveId=setInterval(async()=>{!this.isSaved&&performance.now()-this.lastChangeDataTm>t&&await this.save()},t)},autoSaveStop(){this.timerSaveId&&(clearInterval(this.timerSaveId),this.timerSaveId=!1),this.lastChangeDataTimerId&&(clearInterval(this.lastChangeDataTimerId),this.lastChangeDataTimerId=!1)}},async mounted(){this.autoSaveStop(),await this.actReqwestCourse({id:this.courseId}),this.autoSaveStart()},unmounted(){this.autoSaveStop()}},D={class:"tw-flex"},P={class:"tw-text-sm tw-text-zinc-500"};function T(t,o,r,l,a,n){const m=e.resolveComponent("v-spacer"),u=e.resolveComponent("v-icon"),d=e.resolveComponent("v-tab"),v=e.resolveComponent("v-tabs"),h=e.resolveComponent("v-card-title"),_=e.resolveComponent("v-divider"),p=e.resolveComponent("StudyProgram"),i=e.resolveComponent("v-window-item"),C=e.resolveComponent("CourseSchedule"),g=e.resolveComponent("v-window"),S=e.resolveComponent("v-card-text"),f=e.resolveComponent("v-card");return e.openBlock(),e.createElementBlock("div",null,[e.createVNode(f,null,{default:e.withCtx(()=>[e.createVNode(h,null,{default:e.withCtx(()=>{var s;return[e.createElementVNode("div",D,[e.createElementVNode("div",null,[e.createElementVNode("div",null,[e.createTextVNode(" Название курса: "),e.createElementVNode("strong",null,e.toDisplayString((s=t.getCourse)==null?void 0:s.name),1)])]),e.createVNode(m),e.createElementVNode("div",P,[e.createElementVNode("div",null,"Количество уроков: "+e.toDisplayString(n.cntLessons),1),e.createElementVNode("div",null,"Количество часов: "+e.toDisplayString(n.cntHours),1)]),e.createVNode(u,{onClick:o[0]||(o[0]=c=>n.save()),style:{"margin-left":"10px"},icon:"mdi-content-save-outline",color:t.isSaved?"success":"error"},null,8,["color"])]),e.createVNode(v,{modelValue:a.tab,"onUpdate:modelValue":o[1]||(o[1]=c=>a.tab=c),"bg-color":""},{default:e.withCtx(()=>[e.createVNode(d,{value:"studyProgram"},{default:e.withCtx(()=>[e.createTextVNode("Программа")]),_:1}),e.createVNode(d,{value:"courseSchedule"},{default:e.withCtx(()=>[e.createTextVNode("Расписание")]),_:1})]),_:1},8,["modelValue"])]}),_:1}),e.createVNode(_),e.createVNode(S,null,{default:e.withCtx(()=>[e.createVNode(g,{modelValue:a.tab,"onUpdate:modelValue":o[2]||(o[2]=s=>a.tab=s)},{default:e.withCtx(()=>[e.createVNode(i,{value:"studyProgram"},{default:e.withCtx(()=>[e.createVNode(p)]),_:1}),e.createVNode(i,{value:"courseSchedule"},{default:e.withCtx(()=>[e.createVNode(C)]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})])}const H=V(I,[["render",T]]);export{H as default};
