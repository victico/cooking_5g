<script setup >
import { SEND_NEW_MESSAGE } from '@/core/services/store/chat.module';
import moment from 'moment-timezone';
import 'moment/locale/es';
</script>
<template>
  <div class="chat-message__content  " :class="{'nochat': !activeChat}">
    <div class="h-100 w-100 d-flex justify-center align-center text-center no-chat-active position-relative" v-if="!activeChat">
      <h2>COOKING 5G <br> Soporte</h2>
      <div class="h-100 w-100 d-flex justify-center align-center sup-overlay">

      </div>
    </div>
    <div class="h-100 w-100  position-relative d-flex flex-column justify-space-between pb-4"  >
      <div class="messageArea__header d-flex align-center justify-space-between pe-5">
        <div class=" d-flex align-center">
          <div class="rounded-circle pa-3  justify-center align-center bg-white me-2 messageArea__header--icon d-none d-md-flex">
            <VIcon icon="mingcute:user-2-fill" size="large" color="primary" />
          </div>
          <div class=" pa-0  justify-center align-center  me-4 messageArea__header--icon d-flex d-md-none" @click="backMessagesList()">
            <VIcon icon="fluent-mdl2:skype-arrow"  color="white" />
          </div>
          <div v-if="activeChat">
            <div class="user-chat__name text-white text-capitalize">
              #{{ activeChat.ticket_number }}
            </div>
            <div class="user-chat__type text-white">
              {{activeChat.title}}
            </div>
            <div class="user-chat__user text-white text-capitalize">
              {{ is_admin == "true" ? activeChat.sender.name : activeChat.receipet.name }}
            </div> 
          </div>
        </div>
        <div class="text-center">
          <h5 class="text-white user-chat__type">Estado:</h5>
          <v-chip :color="activeChat.status != 0 ? 'success' : 'secondary'" variant="elevated" v-if="activeChat">
           <span class="user-chat__type"> 
            {{ activeChat.status != 0 ? 'ABIERTO' : 'CERRADO' }} 
           </span>
          </v-chip>
        </div>
      </div>
      <div class="px-3 messagesArea" id="messagesArea"  >
        <template v-if="messages.length > 0"> 

          <div class="d-flex message__text-content flex-column mb-1 " v-for="(message, index) in messages" :id="'message_index_'+index" :key="index" :style="'order:'+(messages.length - index)">
            <div class="normalMessage mt-2" :class="{'visit': message.sender_id == activeUser.user.id, 'visit': message.sender_id == activeUser.user.id }">
              <div class=" elevation-24" :class="{'visited-message': message.sender_id == activeUser.user.id, 'local-message': message.sender_id !== activeUser.user.id, }" v-html="message.message">
              </div>
            </div>
            <div :class="{'text-end': message.sender_id == activeUser.user.id}">
              <h6 class="ms-2" :class="{'me-2': message.sender_id == activeUser.user.id}">
                {{ moment(message.created_at).format('h:mm A')  }}
              </h6>
            </div>
          </div>
        </template>
      </div>
      <div class="d-flex align-center newMessage__area w-100 py-0 px-md-5 px-2" v-if="activeChat && activeChat.status != 0 ">
        <textarea  class="elevation-24" name="" id="" placeholder="Escribe un mensaje" v-model="newMessage" cols="1" rows="1"></textarea>
        <v-btn icon="iconoir:send" variant="tonal" color="primary" class="ms-2" @click="sendNewMessage()"/>
      </div>
    </div>
    <v-snackbar
      v-model="snackShow"
      :color="snackType"
      rounded="pill"
      :timeout="snacktimeOut"
      width="max-content"
      class="text-center"
    >
     <h4 class="text-white w-100 text-center">
       {{snackMessage}}
     </h4>
        <template
          v-slot:actions
        >
        <VBtn  color="white" class="text-white" @click="snackShow=false"> Cerrar</VBtn>
        </template>
    </v-snackbar>
  </div>
</template>
<style lang="scss"  scoped>
.newMessage_text{
  background: rgba(71, 57, 38, 0.363)!important;
  border-radius: 7px;
}
.messagesArea{
  height: 80%;
  overflow-x: hidden;
  overflow-y: auto;
  display: inline-flex;
  flex-direction: column;
}
textarea{
  width: 100%;
  height: max-content;
  background: rgb(207, 207, 207);
  border-radius: 20px;
  overflow: visible;
  outline: none;
  resize: none;
  padding: 13px;
  color: black;
  &::placeholder{
    color:#8592A3!important
  }
}
.messageArea__header{
  height: max-content;
  background: rgb(226, 101, 17);
  width: 100%;
  padding: 10px;
  &--icon{
    width: 50px;
    height: 50px;
  }
}
.newMessage__area{
  height: 40px;
  background: transparent;
}
.chat-message__content{
  width: 100%;
  height: 100%;
  border-top-right-radius: 10px;
  border-bottom-right-radius: 10px;
  background: rgba(180, 149, 107, 0.199)!important;
  transition: all 0.2s ease-in;
  border: 0.5px solid rgb(226, 101, 17);
}
.nochat{
  background: rgba(238, 168, 76, 0.363)!important;
}
.no-chat-active{
  opacity: 0.2
}
.message__text-content{
  justify-content: start;
  width: 100%;
}
.message__text-content:has(> .visit){
  align-items: end;
  width: 100%;

}
.normalMessage{
  max-width: fit-content;
  background: #696969;
  border-radius: 15px;
  position: relative;

}
.visit{
  background: #cf6123;
  position: relative;
}
.local-message{
  border-radius: 15px;
  width: fit-content;
  padding: 10px;
  color: white;
  max-width: 100%;
  &::before{
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    top: calc(50% - 0.7rem);
    left: -9px;
    border-bottom: solid 0.8rem transparent;
    border-right: solid 0.8rem #696969;
    border-top: solid 0.8rem transparent;
  }
}
.visited-message{
  border-radius: 15px;
  width: fit-content;
  padding: 10px;
  color: white;
  max-width: 100%;
  &::after{
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    top: calc(50% - 0.7rem);
    right: -8px;
    border-bottom: solid 0.8rem transparent;
    border-left: solid 0.8rem #cf6123;
    border-top: solid 0.8rem transparent;
  }

}
.sup-overlay{

  position: absolute;
  bottom: 0;
  top: 0;
  right: 0;
  left: 0;

}
.user-chat__name{
  font-size: 1.1rem;
}
.user-chat__type{
  font-size: .8rem;
  font-weight: 600;
}
.user-chat__user {
  font-size: 0.7rem;
  font-weight: 600;

}
@media screen and (max-width: 780px){
  
  .messagesArea{
    height: 75%;
  }
  .user-chat__name{
    font-size: 1rem;
  }
  .user-chat__type{
    font-size: .7rem;
    font-weight: 600;
  }
  .chat-message__content{
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
  }
  .newMessage__area{
    height: max-content;
  }
  .messageArea__header{
    height: max-content;
    &--icon{
      width: 40px;
      height: 40px;
    }
  }
  .local-message{
    font-size: 0.8rem;
  }
  .visited-message{
    font-size: 0.8rem;
  }
}
</style>
<script>
export default {
  name:'MessageChatAreaComponent',
  data:() => {
    return {
      newMessage:'',
      snackShow:false,
      snackMessage:'',
      snackType:'',
      snacktimeOut:2000,
      messages:[],
      is_admin: window.localStorage.is_admin ,
    };
  },
  methods:{
    sendNewMessage(){
      if (this.newMessage && this.newMessage.trim() !== '') {
        const data = {
          id: this.activeChat.id,
          data:{
            message:this.newMessage,
            idUnique: window.localStorage.user_unique_id,
          }
        }
        this.$store.dispatch(SEND_NEW_MESSAGE, data)
        .then((data) => {
          this.newMessage = ''
        })
        return
      }
      this.newMessage = ''
      this.showSnackbar('error', 'No se permiten mensajes sin contenido')
    },
    showSnackbar(type, messagge){
      this.snackShow = true;
      this.snackType = type
      this.snackMessage = messagge
    },
    backMessagesList(){
      this.emitter.emit('displayUserChatList', false)
    },
  },
  mounted(){
    // if(this.activeChat){
      // setTimeout(() => {
        
      //   window.Echo.channel('chatMessages'+this.activeChat.id)
      //   .listen('RealTimeChatMessage',(e)=>{
      //     console.log(e)
      //     console.log('ESCRIBIENDO.....')
      //   })
      // }, 10000);
    // }
  },
  created(){
    this.emitter.on("displayLastMessagge", () => {
      setTimeout(()=>{
        var objDiv = document.getElementById("messagesArea");
        objDiv.scrollTop = objDiv.scrollHeight;
      },100)
    })
    this.emitter.on("getMessages", (messages) => {
      this.messages = messages;
    })
  },
  computed: {
    activeUser() {
      return this.$store.state.user
    },
    activeChat() {
      return this.$store.state.activeChatID
    },
  },
}
</script>