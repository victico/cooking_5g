
// import 'bootstrap/dist/css/bootstrap.min.css';
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import './@core/scss/template/index.scss'
import './@layouts/styles/index.scss'
import './styles/styles.scss'
import { createApp } from 'vue'
import { func } from '@/core/services/utils/utils.js'
import { CHECK_TOKEN, SET_TOKEN } from "@/core/services/store/auth.module";
import axios from "axios";
import VueAxios from "vue-axios";
import store from "@/core/services/store/index.js";
import router from "./router.js";
import middlewarePipeline from './middlewares/middlewarePipeline';
import "bootstrap";
import mitt from 'mitt'
loadFonts()
const emitter = mitt()
const app = createApp(App)

// Create vue app
router.beforeEach(async (to, from, next) => {
  // Caso 1
  document.title = to.meta.title + ' - Cooking 5G'
  if (!to.meta.middleware) {
    return next()
  }

  store.dispatch(CHECK_TOKEN).then((data)=>{
   if(data.data.code !== 200){
    store.commit(SET_TOKEN,data.data.new_token)
   } 
  });


  setTimeout(() => {
    let trashElement = [document.querySelectorAll('.modal-backdrop'), document.querySelectorAll('.tooltip')];

    trashElement.forEach((item)=>{
      item.forEach(element => document.querySelector('body').removeChild(element))
    })
  }, 200);
  // // Caso 2

  // if (window.event.type == 'popstate'){  
  //   next(false);
  // }else{  
  //     next(); 
  // }
  // Caso 3 

    emitter.emit('displayOverlayLoad', true)
    const middleware = to.meta.middleware
    const context = {
      to,
      from,
      next,
      store
    }
    
    return middleware[0]({
      ...context,
      next: middlewarePipeline(context, middleware, 1)
    })
});

// 


// window.localStorage.clear()
// console.log(store)



app.use(VueAxios, axios);
app.use(vuetify)
app.use(router)
app.use(store)
app.use(func)
app.config.globalProperties.emitter = emitter


// app.axios.defaults.baseURL = import.meta.env.VUE_APP_BACKEND_URL 
app.config.globalProperties.$helper = func;
// console.log(app)
app.mount('#app')
