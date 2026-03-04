<template>
    <q-page class="q-pa-md">
      <q-card flat>
        <q-card-section class="bg-grey-1">
          <div class="row flex flex-center">
            <q-item-label class="text-subttile2 text-bold q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
            <div class="col q-pa-xs" align="left">
              <q-select @input="showbyCycle" class="col" dense standard :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
                >
              </q-select>
            </div>
            <q-space />
            <div align="right">
              <q-radio keep-color v-model="vslaCurrency" :val="vslaCurrency" :label="vslaCurrency" color="orange" />
            </div>
              
          </div>
        </q-card-section>
              
        <q-separator></q-separator>
        <q-tabs v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink>
          <q-tab dense size="12px" flat name="ownbalance" label="Mon compte" no-caps icon="account_balance_wallet" class="text-negative"/>
          <q-tab v-if="typeUser !== 'Ordinary member'" no-caps name="membersbalance" label="Comptes des membres" icon="balance" class="text-light-blue-10"/>
          <q-tab dense name="groupbalance" label="Compte du groupe" icon="account_balance" no-caps class="text-accent"/>
          
        </q-tabs>
        <q-separator></q-separator>
        <q-tab-panels v-model="tab">
          <q-tab-panel name="groupbalance">
            <q-card class="rounded-borders q-pa-sm">
              <q-toolbar class="bg-light-blue-10 text-white shadow-2">
                <q-toolbar-title>Balance du groupe</q-toolbar-title>
              </q-toolbar>
              <q-separator />
                <q-item clickable v-ripple style="border-right: solid rgb(30, 85, 16) 10px;"> 
                  <q-item-section side >
                    <q-avatar rounded size="48px">
                      <q-icon name="volunteer_activism" color="cyan-10"></q-icon>
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    Dons reçus
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="text-h6 text-bold text-grey"> {{ TotalDons }} {{ vslaCurrency }}</q-item-label>
                  </q-item-section>
                  
                </q-item>
                <q-separator />

                <q-item clickable v-ripple style="border-right: solid rgb(9, 27, 118) 10px;"> 
                  <q-item-section side >
                    <q-avatar rounded size="48px">
                      <q-icon name="waving_hand" color="light-blue-10"></q-icon>
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    Fonds social
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="text-h6 text-bold text-grey">{{ TotalSocial }} {{ vslaCurrency }}</q-item-label>
                  </q-item-section>
                  
                </q-item>

                <q-separator />

                <q-item clickable v-ripple style="border-right: solid orange 10px;"> 
                  <q-item-section side >
                    <q-avatar rounded size="48px">
                      <q-icon name="flutter_dash" color="amber"></q-icon>
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    Penalités
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="text-h6 text-bold text-grey">{{ TotalPenalites }} {{ vslaCurrency }}</q-item-label>
                  </q-item-section>
                  
                </q-item>

                <q-separator />

                <q-item clickable v-ripple style="border-right: solid rgb(182, 24, 24) 10px;"> 
                  <q-item-section side >
                    <q-avatar rounded size="48px">
                      <q-icon name="how_to_vote" color="negative"></q-icon>
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    Dépenses
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="text-h6 text-bold text-grey">{{ TotalDepenses }} {{ vslaCurrency }}</q-item-label>
                  </q-item-section>
                  
                </q-item>

                <q-separator />
                <q-card-actions align="around">
                  <q-item-label class="text-h5 text-bold text-center text-light-blue-10 q-pa-sm">Solde : {{ TotalSolde }} {{ vslaCurrency }}</q-item-label>
                </q-card-actions>
            </q-card>
          </q-tab-panel>

          <q-tab-panel name="membersbalance">
            <q-card flat>
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                :data="mbbalances"
                :columns="columns"
                row-key="name"
                binary-state-sort
                :filter="filter" 
                grid
                hide-header
                selection="single"
                :selected.sync="selected"
                >
  
                <template v-slot:item="props">
                  <div
                    class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
                    :style="props.selected ? 'transform: scale(0.95);' : ''"
                  >
                    <q-card :class="props.selected ? 'bg-grey-2' : ''">
                      <q-card-section class="text-subtitle2">
                        <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                      </q-card-section>
                      <q-separator />
                      <q-list dense>
                        <q-item v-for="col in props.cols.filter(col => col.name !== 'desc')" :key="col.name">
                          <q-item-section>
                            <q-item-label>{{ col.label }}</q-item-label>
                          </q-item-section>
                          <q-item-section side v-if="col.name != 'Encour' && col.name != 'Penality' && col.name != 'Recu'">
                            <q-item-label caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                          <q-item-section side  v-if="col.name == 'Encour' || col.name == 'Penality' || col.name == 'Recu'">
                            <q-item-label class="text-negative" caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <q-separator></q-separator>
                      <!-- <q-card-actions align="around">
                        <q-item-label class="text-h6 text-light-blue-10 text-center q-pa-sm">Solde : {{ props.row.Solde }} {{ vslaCurrency }}</q-item-label>
                      </q-card-actions> -->
                      <q-card-actions align="right">
                        <q-item-label class="text-overline text-bold text-light-blue-10 text-center q-pa-sm">Solde : </q-item-label>
                        <q-badge rounded class="text-h6 text-light-blue-10" color="grey-3 text-bold">{{ vslaCurrency }}</q-badge>
                        <q-badge v-if="props.row.Solde >= 0" rounded class="text-h6 text-bold" outline color="cyan-8">{{ props.row.Solde }}</q-badge>
                        <q-badge v-if="props.row.Solde < 0" rounded class="text-h6 text-bold" outline color="negative">{{ props.row.Solde }}</q-badge>
                      </q-card-actions>
                     
                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input dense outlined debounce="500" v-model="filter" placeholder="Rechercher" class="col">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                    </q-input>
                </template>
  
            </q-table>
            </q-card>
          </q-tab-panel>

          <q-tab-panel name="ownbalance">
            <q-card class="rounded-borders q-pa-sm">
              <q-toolbar class="text-negative shadow-2">
                <q-toolbar-title class="text-bold text-overline">Votre balance dans le groupe</q-toolbar-title>
              </q-toolbar>
              <q-separator />
                
                <q-table bordered flat separator="cell" class="col q-pa-sm"
                :data="mybalance"
                :columns="columns"
                hide-pagination
                row-key="name"
                binary-state-sort
                :filter="filter" 
                grid
                hide-header
                selection="single"
                :selected.sync="selected"
                >
  
                <template v-slot:item="props">
                  <div
                    class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
                    :style="props.selected ? 'transform: scale(0.95);' : ''"
                  >
                    <q-card :class="props.selected ? 'bg-grey-2' : ''">
                      <q-card-section class="text-subtitle2">
                        <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                      </q-card-section>
                      <q-separator />
                      <q-list dense>
                        <q-item v-for="col in props.cols.filter(col => col.name !== 'desc')" :key="col.name">
                          <q-item-section>
                            <q-item-label>{{ col.label }}</q-item-label>
                          </q-item-section>
                          <q-item-section side v-if="col.name != 'Encour' && col.name != 'Penality' && col.name != 'Recu'">
                            <q-item-label caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                          <q-item-section side  v-if="col.name == 'Encour' || col.name == 'Penality' || col.name == 'Recu'">
                            <q-item-label class="text-negative" caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <q-separator></q-separator>
                      <q-card-actions align="right">
                        <q-item-label class="text-overline text-bold text-light-blue-10 text-center q-pa-sm">Solde : </q-item-label>
                        <q-badge rounded class="text-h6 text-light-blue-10" color="grey-3">{{ vslaCurrency }}</q-badge>
                        <q-badge rounded class="text-h6 text-bold" outline color="accent">{{ props.row.Solde }}</q-badge>
                      </q-card-actions>

                    </q-card>
                  </div>
                </template>
  
            </q-table>
            </q-card>
          </q-tab-panel>

         
        </q-tab-panels>
      </q-card>
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'
  const columns = [
        { name: 'Epargne', align: 'right', label: 'Epargne totale', field: 'Epargne', sortable: true },
        { name: 'Interet', align: 'right', label: 'Interêt sur crédits remboursés', field: 'Interet', sortable: true },
        { name: 'Interet', align: 'right', label: 'Interêt sur crédits encours', field: 'Interet', sortable: true },
        { name: 'Encour',  align: 'right', label: 'Crédits encours', field: 'Encour', sortable: true },
        { name: 'Penality',  align: 'right', label: 'Penalités/Amendes', field: 'Penality', sortable: true },
        { name: 'Recu',  align: 'right', label: 'Retiré au partage', field: 'Recu', sortable: true },
        { name: 'Name', align: 'right', label: 'Membre', field: 'Name' }
        
    ]
  export default {
    
    data () {

      return {
        tab: 'ownbalance',
        columns,
        accounts: [],
        cycles: [],
        cycleFilter: '',
        mbbalances: [],
        mybalance: [],
        gpbalance: [],
        filter: '',
        selected: [],
        TotalDons: null,
        TotalSocial: null,
        TotalPenalites: null,
        TotalDepenses: null,
        TotalSolde: null,
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8),
        vslaCurrency: 'CDF'
      }
    },
    created () {
        this.listCycles()
    },
    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Required')
            }, 800)
            })
        },
        listaccounts () {
            axios.get(this.$urlServer + 'accountControllers/listAccount.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.accounts = dataFront.data
            })
        },
        listMbBalances () {
            axios.get(this.$urlServer + 'shareout/balancePerCycle.php', {
              params: {
                cycleId: this.cycleFilter
              }
            }).then((dataFront) => {
                this.mbbalances = dataFront.data
            })
        },
        listMyBalance () {
            axios.get(this.$urlServer + 'shareout/balancePerMember.php', {
              params: {
                cycleId: this.cycleFilter,
                memberId: this.memberId
              }
            }).then((dataFront) => {
                this.mybalance = dataFront.data
            })
        },
        listGpBalance () {
            axios.get(this.$urlServer + 'shareout/groupBalance.php', {
              params: {
                cycleId: this.cycleFilter
              }
            }).then((dataFront) => {
                this.gpbalance = dataFront.data
                this.TotalDons = dataFront.data[0].Dons
                this.TotalSocial = dataFront.data[0].Socials
                this.TotalPenalites = dataFront.data[0].Penalites
                this.TotalDepenses = dataFront.data[0].Depenses
                this.TotalSolde = dataFront.data[0].Solde
            })
        },
        listCycles () {
            axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.cycles = dataFront.data
            })
        },
        showbyCycle() {
          this.listaccounts()
          this.listMbBalances()
          this.listMyBalance()
          this.listGpBalance()
        }
    }
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    