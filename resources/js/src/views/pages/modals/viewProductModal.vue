<script setup>
import moment from 'moment';
import { mapGetters } from "vuex";

import { func } from '@/core/services/utils/utils.js'
const props = defineProps({
  product: Object,
})

</script>
<template>
  <div class="modal animate__animated animate__slideInLeft" id="viewProduct" tabindex="-1" aria-labelledby="showCartLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg mt-0 ma-0" style="width: 100%; height: 100vh;">
      <div class="modal-content h-100">
        <VCol
            cols="12"
            class="pa-0 d-flex justify-center"
            style="position: relative;"
          >
          <VCol
              cols="12"
              class="pa-0"
            >
            <VCard class="modal__content h-100 rounded-0">
              <div class="modal__close-button__cart" >
                <v-col  class="pa-0 pe-4">
                  <v-btn icon="mingcute:close-fill" class="bg-secondary" @click="hideModal()" ></v-btn>
                </v-col>
              </div>
              <div class="d-flex justify-space-between  flex-column pa-2 px-3 pa-md-5 ">
                <VDivider :vertical="$vuetify.display.mdAndUp" />

                <div class="w-100">
                  <VRow class="ma-0 pa-0">
                    <VCol cols="12" md="4" class="justify-center d-grid">
                      <div class="ma-auto mx-0 pa-5 pb-0">
                        <VImg
                          width="200"
                          height="200"
                          class="rounded"
                          :src="'/'+product.product.img "
                        />
                      </div>
                    </VCol>
                    <VCol cols="12" md="8" class="mt-0 pt-0 mb-4">

                      <VCardItem class="px-1 ">
                        <VCardTitle>{{ product.product.title }}</VCardTitle>
                      </VCardItem>

                      <VCardText class="px-1">
                        {{ product.product.description}}
                      </VCardText>
                      <div   class="mt-0" style="border-top: 1px solid rgba(119, 119, 119, 0.356)" v-if=" !product.viewOnly" >

                        <VCardText class="text-subtitle-1 py-4 px-1" v-if="getCurrentAccount.rol_id !== 3">
                          <span class="font-weight-medium">Fecha de entrada:</span> <span class="font-weight-bold">
                            {{ moment(product.created_at_lote).format('DD-MM-YYYY')  }}
                          </span>
                        </VCardText>
                        <VCardText class="text-subtitle-1 py-4 px-1" v-if="getCurrentAccount.rol_id !== 3">
                          <span class="font-weight-medium">Número Lote:</span> <span class="font-weight-bold">
                            {{ 
                                product.lote_code
                                  
                            }}</span>
                        </VCardText>
                        
                        <VCardText class="text-subtitle-1 py-4 px-1 d-flex align-center">
                          <span class="font-weight-medium">Stock total: </span> 
                          <span class="font-weight-bold mx-2" v-if="product.quantity > 0">
                            <v-chip :class=" product.product.total_stock < 0 ? 'bg-error' : product.product.total_stock > 30 ? 'bg-success' : 'bg-warning'">

                            {{func.numberFormat(product.product.total_stock)}} {{product.product.type_of_unit }}
                            </v-chip>
                          </span>
                          <span class="font-weight-bold text-error" v-else>
                            SIN STOCK
                          </span>
                        </VCardText>
                        <VCardText class="text-subtitle-1 pt-0 px-1 d-flex align-center" v-if=" getCurrentAccount.rol_id !== 3">
                          <div class="font-weight-medium my-0">Fecha de vencimiento:</div>
                          <div class="font-weight-bold mx-2">
                            <v-chip :class=" Math.round(moment.duration(moment(product.due_date).diff(new moment())).as('days') ) < 0 ? 'bg-error' : Math.round(moment.duration(moment(product.due_date).diff(new moment())).as('days') ) > 30 ? 'bg-success' : 'bg-warning'">
                              {{ 
                                Math.round(moment.duration(moment(product.due_date).diff(new moment())).as('days') ) < 0 
                                ? moment(product.due_date).format('DD-MM-YYYY') + ' (LV)'
                                : moment(product.due_date).format('DD-MM-YYYY') 
                              }}
                            </v-chip>
                          </div>
                        </VCardText>
                        
                      </div>
                      
                    </VCol>
                  </VRow>
                  <div  v-if=" !product.viewOnly" >
                    <div v-if=" getCurrentAccount.rol_id !== 3 ">
  
                      <div style="border-top: 1px solid rgba(119, 119, 119, 0.356)" v-if="product.product.is_dismantling !=0 && countDismantlingActive(product.product.dismantling) > 0">
                        <VCardText class="text-subtitle-1 pb-4">
                          <span class="font-weight-medium">Cantidad de despices:</span> <span class="font-weight-bold">{{ countDismantlingActive(product.product.dismantling) }}</span>
                        </VCardText>
                        <VCardText class="text-subtitle-1">
                          <p class="mb-0">Despieces:</p> 
                          <div class="d-block d-md-flex">
                            <b v-for="item in product.product.dismantling" v-bind:key="item.id">
                              <p class="mb-0 ms-2 mt-3" v-if="item.products_pieces" >  
                                <b class="d-inline-flex d-md-none">*</b> {{ item.products_pieces.title}}: {{item.quantity}} {{ item.quantity > 1 ? 'Piezas' : 'Pieza' }} <b class="d-none d-md-inline-flex">||</b>
                              </p> 
                            </b>
                          </div>
                        </VCardText>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </VCard>
          </VCol>
        </VCol>
      </div>
    </div>
  </div>
</template>
<script>

export default {
  mounted(){
  },
  methods:{
    hideModal(){
      this.$emit('hiddenModal')
    },
    countDismantlingActive(dismantlings){
      let count = 0;
      dismantlings.forEach((data)=>{
        if(data.products_pieces) count ++
      })
      
      return count
    }
  },
  computed: {
    ...mapGetters(["currentUser"]),

    getCurrentAccount() {
      return this.currentUser;
    },
  },


};

</script>