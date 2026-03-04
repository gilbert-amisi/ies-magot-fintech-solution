<template>
    <q-page class="q-pa-sm">
        <!-- <q-card class="q-pa-xs">
                <q-item-label header class="text-h5 text-bold text-center text-overload">GROUPE {{ vslaName }}</q-item-label>
            </q-card> -->
        <q-card class="q-pa-xs" bordered>

            <q-card-section class="bg-light-blue-10 text-white">
                <q-item>
                    <q-item-section avatar>
                    <q-avatar>
                        <q-icon name="diversity_2" size="36px"></q-icon>
                    </q-avatar>
                    </q-item-section>

                    <q-item-section>
                        <q-item-label class="text-subtitle1 text-bold">GROUPE {{ vslaName }}</q-item-label>
                        <q-item-label class="text-overline">
                            {{ vslaLocation }}
                        </q-item-label>
                    </q-item-section>
                </q-item>

            </q-card-section>

            <q-separator />

              <q-card-section v-for="c in cycle" :key="c.value" class="bg-grey-1 flex flex-center q-pa-md">
                
                <q-card bordered class="q-pa-sm">
                  <q-card-section class="row bg-grey-1">
                    <div class="text-h6 q-mb-md"> {{ c.label }} {{ c.Status.toLowerCase() }}</div>
                    <q-space></q-space>
                    <q-spinner-dots v-if="c.Status == 'Encours'" color="orange" size="4em"  />
                  </q-card-section>
                  <q-separator></q-separator>
                  <q-card-section class="row no-wrap q-pa-md">

                    <div class="column">
      
                      <q-toggle v-model="setStart" :label="c.Start" />
                      <q-toggle v-model="setEnd" :label="c.End" />
                      
                    </div>

                    <q-separator vertical inset class="q-mx-lg" />
                    <div class="column items-center">
                          <q-spinner-clock color="brown" size="5em" />
                          
                    </div>
                  </q-card-section>
                  <q-separator></q-separator>
                  <q-card-actions align="around">
                    <q-btn v-if="c.Status=='Encours'" push rounded side no-caps dense icon="close" color="negative" label="Cloturer ce cycle"></q-btn>
                    <q-btn push rounded side no-caps dense icon="add" color="primary" label="Nouveau cycle" to="/cycle"></q-btn>
                  </q-card-actions>
                  </q-card>
                
              </q-card-section>
            
            <q-card-section>
            </q-card-section>
            <q-separator></q-separator>
            <q-card-actions align="around" class="bg-grey-1">
                <q-item-label>Vous êtes connecté en tant que <b>{{ username }},</b> </q-item-label>
                <q-item-label class="text-center">{{ typeUser}} </q-item-label>
            </q-card-actions>
        </q-card> 
    </q-page>
  </template>
  <script>
  import axios from 'axios'
  import vue from 'vue'
  import mysession from 'vue-session'
  vue.use(mysession)
  export default {
    name: 'PageIndex',
    data () {
  
      return {
        cycle: [],
        setStart: true,
        setEnd: false,
        nbMember: null,
        nbAccount: null,
        nbCreditRequest: null,
        Outstanding: null,
        nbDonation: null,
        nbMeeting: null,
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8),
        vslaLocation: this.$session.get(9)
      }
    },
    created () {
        this.listCycle(),
        this.statvsla()
    },
    methods: {
        listCycle () {
            axios.get(this.$urlServer + 'cycleControllers/listCycle.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.cycle = dataFront.data
            })
        },
        statvsla () {
          axios.get(this.$urlServer + 'vslaControllers/statisticsVSLA.php', {
              params: {
                  vslaId: this.vslaId
              }
          }).then((dataFront) => {
                this.nbMember = dataFront.data[0].nbMember
                this.nbAccount= dataFront.data[0].nbAccount
                this.nbCreditRequest= dataFront.data[0].nbCreditRequest
                this.Outstanding= dataFront.data[0].Outstanding
                this.nbDonation= dataFront.data[0].nbDonation
                this.nbMeeting= dataFront.data[0].nbMeeting
          })
      }

    }
  }
  </script>
  