<template>
  <div>
    <VCard class="pa-0 ma-0">
      <VRow class="chat-areacomponent ma-0 pa-0 d-none d-md-flex" v-if="displayWidth > 780">
        <VCol cols="12"  md="4" class="pa-0 d-md-flex chat-list flex-column " >
          <userList />
          <VRow class="justify-center my-2 "  v-if="currentUser.user.rol_id == 3 ">
            <VCol cols="10" >
              <VBtn @click="showModal('createNewTicket')" color="primary" class="w-100 ">
                <VIcon icon="bx-plus"/> Crear nuevo ticket
              </VBtn>
            </VCol>
          </VRow>
        </VCol>
        <VCol class="d-md-flex pa-0 message-area"  cols="12" md="8">
          <messageChat />
        </VCol>
      </VRow>
      <VRow class="chat-areacomponent ma-0 pa-0 d-flex d-md-none" v-else>
        <VCol cols="12"  md="4" class="pa-0 d-md-flex chat-list  flex-column" :class="{'borrao': deplay }">
          <userList />
          <VRow class="justify-center my-2 " v-if="currentUser.user.rol_id == 3 ">
            <VCol cols="10" >
              <VBtn @click=" showModal('createNewTicket')" color="primary" class="w-100 ">
                <VIcon icon="bx-plus"/> Crear nuevo Ticket
              </VBtn>
            </VCol>
          </VRow>
        </VCol>

        <VCol class="d-md-flex pa-0 message-area d-flex"  cols="12" md="8" :class="{'activao': !deplay == false }">
          <messageChat />
        </VCol>
      </VRow>
    </VCard>
    <div class="modal animate__animated animate__fadeInDown"  id="createNewTicket" tabindex="-1" aria-labelledby="cancelOrderLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg mt-10" >
        <div class="modal-content">
          <VCol
            cols="12"
            class="pa-0 d-flex justify-center"
            style="position: relative;"
          >
            <VCol
              cols="12"
            >
              <VCard class="modal__content">
                <div class="modal__close-button" >
                  <v-col class="pa-0 pe-4">
                    <v-btn icon="mingcute:close-fill" class="bg-secondary" @click="hideModal()" ></v-btn>
                  </v-col>
                </div>
                <div class="d-flex justify-space-between  flex-column pa-2 pa-md-5 ">
                  <VCardItem class="justify-center w-100  py-md-6  py-4  my-5  ">
                    <VCardTitle class="text-2xl font-weight-bold">
                      <div class="card-title d-flex ">
                        <div class="form-title__part1">Crear Ticket</div>
                      </div>
                    </VCardTitle>
                  </VCardItem>
                  <VCardText class="w-100 pb-5 px-3 px-md-6">
                    <VForm  id="new_user_form">
                      <VRow class="align-center">
                        <VCol cols="12" class="form-group mt-md-2 mt-0 pt-1 pt-md-2">
                          <v-select
                            v-model="newTicket.type"
                            :items="typeTicket"
                            item-title="title"
                            item-value="value"
                            label="Asunto"
                            return-object
                            single-line
                          ></v-select>
                        </VCol>
                        <VCol cols="12"  class="form-group mt-md-2 mt-0 pt-1 pt-md-2">
                          <v-textarea
                            label="Mensaje"
                            auto-grow
                            variant="outlined"
                            rows="3"
                            autocomplete="off"
                            row-height="25"
                            shaped
                            name="new_user_address"
                            v-model="newTicket.message"
                          ></v-textarea>
                        </VCol>
                      </VRow>
                      <VRow class="ma-0 pa-0  mt-8 align-center">
                        <VCol cols="12" md="4" offset-md="4" class="mt-0 py-0 px-0">
                          <v-col cols="auto" class="">
                            <VBtn  color="primary" class="w-100 " @click="storeChat()">Crear</VBtn>
                          </v-col>
                        </VCol>
                      </VRow>
                    </VForm>
                  </VCardText>
                </div>
              </VCard>
            </VCol>
          </VCol>
        </div>
      </div>
    </div>
    <template v-if="activeChat" >
      <div class="modal animate__animated animate__fadeInDown pe-0" id="optionsModal" tabindex="-1" aria-labelledby="cancelOrderLabel" aria-hidden="true">   
        <div class="modal-dialog modal-lg mt-10">
          <div class="modal-content">
            <VCol
              cols="12"
              class="pa-0 d-flex justify-center"
              style="position: relative;"
            >
              <VCol
                cols="12"
                class="px-2"  
              >
                <VCard class="modal__content">
                  <div class="modal__close-button" >
                    <v-col class="pa-0 pe-4">
                      <v-btn icon="mingcute:close-fill" class="bg-secondary" @click="hideModal()" ></v-btn>
                    </v-col>
                  </div>
                  <div class="d-flex justify-space-between  flex-column pa-5 pa-md-5 ">
                    <VRow  class="mb-2 ma-0">
                      <VCol cols="12" class="py-2">
                        <div class="text-center d-flex flex-column  h-100" style="position: relative;">
                          <h3 class="mt-4 mb-0">{{ activeChat.title }}</h3>
                          <h4 class="mt-1">Por: {{ activeChat.sender.name }}</h4>
                          <div class="d-flex  justify-center mx-5  align-center h-50 mt-4 " style="box-sizing:content-box;">
                            <div class="d-flex flex-column justify-center align-center" >
                              <v-btn size="large" class="d-block mx-8 shadow-button" color="primary" @click="chageStatus(activeChat)" icon="solar:inbox-archive-linear" />
                              <h5 class="mt-1" >{{ activeChat.status == 1 ? 'Cerrar Ticket' : 'Reactivar ticket' }}</h5>
                            </div>
                            <div class="d-flex flex-column justify-center align-center"  v-if="activeChat.reference_id">
                              <v-btn size="large" class="d-block mx-8 shadow-button" color="primary" @click="$router.push('/products')" icon="solar:box-outline" />
                              <h5 class="mt-1" >Ver Producto</h5>
                            </div>
                            <div class="d-flex flex-column justify-center align-center" >
                              <v-btn size="large" class="d-block mx-8 shadow-button" color="error" @click="deleteChat(activeChat.id)" icon="mi:delete" />
                              <h5 class="mt-1" >Eliminar</h5>
                            </div>
                          </div>
                        </div>
                      </VCol>
                    </VRow>
                    <VDivider  />
                  </div>
                </VCard>
              </VCol>
            </VCol>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script>
  import userList from '@/layouts/components/UserChatList.vue'
  import messageChat from '@/layouts/components/MessageChatArea.vue'
  import * as bootstrap from 'bootstrap'
  import { STORE_CHAT, CHANGE_STATUS_CHAT, DELETE_CHAT } from  '@/core/services/store/chat.module'

export default {
  data(){
    return{
      displayWidth: window.screen.width,
      id_unique : localStorage.getItem('user_unique_id'),
      deplay: false,
      modal: '',
      newTicket:{
        type: 
        {
          value: 0,
          title:'Duda/Inquietud'
        },
        message:'',
      },
      typeTicket:[
        {
          value: 0,
          title:'Duda/Inquietud'
        },
        {
          value: 1,
          title: 'Información de pedido'
        },
        {
          value: 3,
          title: 'Información de producto'
        },
        {
          value: 4,
          title: 'Reportar un error'
        },
      ]
    }
  },
  components: {
    userList,
    messageChat,
  },
  methods:{
    showModal(modal) {
      try {
        this.hideModal()
      } catch (error) {
        
      }
      this.modal = new bootstrap.Modal(document.getElementById(modal), {
        keyboard: false,              
        backdrop:'static'
      })

      this.modal.show()
    },
    hideModal(){
      this.modal.hide()
    },
    storeChat(){
      const newMessage = new FormData()
      newMessage.append('type', this.newTicket.type.value)
      newMessage.append('title', this.newTicket.type.title)
      newMessage.append('message', this.newTicket.message)

      this.$store.dispatch(STORE_CHAT, newMessage)
      .then((data) => {
        setTimeout(() => {
          this.hideModal()
          this.clearForm()
        }, 1000);
      }).catch((err) =>  {
        
      })
    },
    chageStatus(chat){
      const data = {
        data:{
          status: chat.status == 0 ? 1 :0,
        },
        id:chat.id
      }
      this.$store.dispatch(CHANGE_STATUS_CHAT, data)
      .then((data) => {
        setTimeout(() => {
          this.emitter.emit('updateChat')
          try{
            this.hideModal()
          }catch{

          }
        }, 1000);
      }).catch((err) =>  {
        
      })
    },
    deleteChat(id){
      console.log(id)
      this.$store.dispatch(DELETE_CHAT, id)
      .then((data) => {
        this.$store.state.activeChatID = null
        setTimeout(() => {
          this.emitter.emit('updateChat')
          this.hideModal()
        }, 200);
      })
    },
    clearForm(){
      this.newTicket = {
        type: {
          value: 0,
          title:'Duda/Inquietud'
        },
        message:'',
      };
    }
  },
  created(){
    this.emitter.emit('displayOverlayLoad', false)

    this.emitter.on("displayUserChatList", (status) => {
      this.deplay  = status
    })
    this.emitter.on("mobileFunction", (status) => {
      this.deplay  = true
    })
    this.emitter.on("shoModal", (modal) => {
      this.showModal(modal)
    })
    this.emitter.on("chatActions", (data) => {
      if(data.action == 'change-status'){
        this.chageStatus(data.value)
        return
      }
      if(data.action == 'delete'){
        this.deleteChat(data.value)
        return
      }
      this.$router.push('/products')
    })
  },
  computed: {
    activeChat() {
      return this.$store.state.activeChatID
    },
    currentUser() {
      return this.$store.state.user
    }
  },
}
</script>
<style lang="scss" >
 .animate__animated{
  animation-duration: 0.5s;
  }
.chat-areacomponent{
  background:  #ffffff25;
  width: 100%;
  height: 85vh;
  display: flex;
  border-radius: 10px;
}
.chat-list{
  height:100%;
  right: 0px;
  position: relative;
  transition: opacity ease-in 0.4s;
  opacity: 1;
  overflow: hidden;
  &.borrao{
    position: absolute;
    opacity: 0;
  }
}
.message-area {
  height:100%;
  overflow: hidden;

}
@media screen and (max-width: 780px){
  .chat-areacomponent{
    height: 76vh;
    overflow: hidden;
  }
  .message-area {
    height:100%;
    position: absolute;
    left: 500px;
    transform: scale(0.8);
    transition: all ease-in 0.4s;

    &.activao{
      position: relative;
      left: 0px;
      transform: scale(1);
    }
  }
}
@media screen and (min-height: 844px){
  .chat-areacomponent{
    height: 85vh;
    overflow: hidden;
  }
}

</style>