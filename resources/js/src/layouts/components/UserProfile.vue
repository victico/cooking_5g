<script setup>
// import avatar1 from '@images/avatars/avatar-1.png'
import { LOGOUT } from "@/core/services/store/auth.module";
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered 
    v-if="user.name"
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      {{  user.name.slice(0,1).toUpperCase() }}

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                  {{  user.name.slice(0,1).toUpperCase() }}
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{  user.name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ user.rol.rol_name }}</VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <!-- <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-user"
                size="22"
              />
            </template>
            <Component
              :is="'RouterLink'"
              to="/acount-settings"
              href="/acount-settings"
            >
              <VListItemTitle>Perfil</VListItemTitle>
            </Component>
            
          </VListItem> -->

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem class="logout" @click="logout()">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>
            <VListItemTitle>Cerrar sesion</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
    </VAvatar>
  </VBadge>
</template>

<script>
import { mapGetters } from "vuex";
import { inject } from "vue";
  export default {
    data(){
      return{
        isReady: false,
      }
    },
    mounted(){
    },
    methods:{
      logout(){
        this.emitter.emit("displayOverlayLoad", true)
        this.$store.dispatch(LOGOUT)
        .then(() => { 
          window.location.href = "/login";
        
        })
        .catch(()=>{ 
          window.location.href = "/login";
        });
      }
      
    },
    computed: {
      ...mapGetters(["currentUser"]),

      user() {
        return this.currentUser;
      }
    }
  };
</script>