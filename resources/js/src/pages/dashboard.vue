<script setup>
import GreetingsComponent from '@/views/dashboard/GreetingsComponent.vue'
import LastSendingOrders from '@/views/dashboard/LastSendingOrders.vue'
import LastPendingOrders from '@/views/dashboard/LastPendingOrders.vue'
import CriticalStockProduct from '@/views/dashboard/CriticalStockProduct.vue'
import finance from '@/views/dashboard/AnalyticsOrderStatistics.vue'
import searchOrder from '@/views/dashboard/searchTrackOrder.vue'
</script>

<template>
  <div>
    <VRow>
      <!-- 👉 Congratulations -->
      <VCol
        cols="12"
        md="8"
        class="px-2 px-md-3"
      >
        <VRow>
          <VCol
            cols="12"
          >
            <GreetingsComponent />
          </VCol>
          <VCol
            cols="12"
            md="12"
            v-if="getCurrentAccount.rol_id == 3 "
          >
            <searchOrder />
          </VCol>
          <VCol
            cols="12"
            md="12"
            v-if="getCurrentAccount.rol_id == 2 || getCurrentAccount.rol_id == 1 "
          >
            <finance />
          </VCol>
          <VCol
            cols="12"
            md="12"
          >
            <LastPendingOrders />
          </VCol>
        </VRow>
      </VCol>
      <VCol
        cols="12"
        sm="4"
        order="1"
        order-sm="2"
        class="text-center px-2 px-md-3"
      >
        <v-card
          elevation="24"
          max-width="444"
          class="mx-auto"
        >
          <v-carousel
            :continuous="false"
            :show-arrows="false"
            hide-delimiter-background
            delimiter-icon="mdi-square"
            height="450"
            cycle
            direction="vertical"
            progress="primary"
            interval="8000000"
          >
            <v-carousel-item
              v-if="getCurrentAccount.rol_id == 1 "
            >
              <VCol
                class="pa-0"
                cols="12 h-100"
              >
                <CriticalStockProduct />
              </VCol>
            </v-carousel-item>
            <v-carousel-item>
              <VCol
                class="pa-0"
                cols="12 h-100"
              >
                <LastSendingOrders />
              </VCol>
            </v-carousel-item>
          </v-carousel>
        </v-card>
      </VCol>
    </VRow>
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/pages/dashboard.scss";

</style>
<script>
  import { mapGetters } from "vuex";
  export default {
    data () {
      return {
      }
    },
    methods:{
    },
    created(){
      this.emitter.emit('displayOverlayLoad', false)
    },
    computed: {
      ...mapGetters(["currentUser"]),

      getCurrentAccount() {
        return this.currentUser;
      },
    },
  };
</script>