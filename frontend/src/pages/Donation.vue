<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
         <div class="text-h6 text-light-blue-10 text-center">DONS ET LEGS REÇUS</div>
        </q-card-section>
        <q-tabs v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink inline-label>
          <q-tab no-caps name="list" label="Liste des dons reçus" icon="bookmarks" class="text-primary"/>
          <q-tab no-caps v-if="typeUser == 'Chairman' || typeUser == 'Secretary'" name="add" label="Enregistrer un don" icon="bookmark_add" class="text-accent" />
          
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="list">
            <q-card flat>
              <q-card-section class="bg-grey-1">
                <div class="row flex flex-center">
                  <q-item-label class="text-h6 q-pa-sm text-light-blue-10">Filtrer par</q-item-label>
                  <div class="col q-pa-xs" align="left">
                    <q-select @input="listdonations" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" label="Cycle" map-options emit-value    
                      >
                    </q-select>
                  </div>
                  <q-space />
                  <div align="right">
                    <q-radio keep-color v-model="vslaCurrency" :val="vslaCurrency" :label="vslaCurrency" color="orange" />
                  </div>
                    
                </div>
              </q-card-section>
  
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                title="Dons et legs enregistrés"
                :data="donations"
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
                        <q-checkbox dense v-model="props.selected" :label="props.row.label" />
                      </q-card-section>
                      <q-separator />
                      <q-list dense>
                        <q-item v-for="col in props.cols.filter(col => col.name !== 'desc')" :key="col.name">
                          <q-item-section>
                            <q-item-label>{{ col.label }}</q-item-label>
                          </q-item-section>
                          <q-item-section side>
                            <q-item-label caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <q-separator></q-separator>
                      <!-- <q-card-actions align="around" v-if="typeUser == 'Chairman' || typeUser == 'Secretary'">
                        <q-btn side flat no-caps dense icon="edit" color="primary" label="Modifier"></q-btn>
                        <q-btn side flat no-caps dense icon="close" color="negative" label="Annuler"></q-btn>
                      </q-card-actions> -->
                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un don" class="col">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                    </q-input>
                </template>
  
            </q-table>
          </q-card>
          </q-tab-panel>

          <q-tab-panel name="add">
            <q-card flat>
              <q-card-section class="bg-accent">
                <q-item-label class="text-subtitle2 text-bold text-white">Enregistrement du don</q-item-label>
              </q-card-section>
              <q-separator></q-separator>
              <q-form class="full-width q-pa-sm">
                <div class="row q-mb-sm">
                    <q-input dense outlined v-model="donor" ref="donor" label="Nom du Donateur" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amount" ref="amount" label="Montant reçu" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de reception" :rules="[ myRule ]" class="col">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy">
                            <q-date v-model="date" mask="YYYY-MM-DD"></q-date>
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                  <q-select dense outlined
                    :options="[
                      'Fonctionnement',
                      'Appui aux AGR',
                      'Autres'
                    ]" 
                    :rules="[myRule]"  v-model="use" ref="use" label="Affectation du don" class="col" emit-value map-options 
                  >
                  </q-select>
                </div>
   
              <div align="right">
                  <q-btn dense
                      @click="addDonation"
                      label="Enregistrer"
                      color="accent"
                      icon="save"
                      outline
                      type="submit">
                  </q-btn>
              </div>
              </q-form>
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'
  
  const columns = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
        { name: 'Amount',  align: 'right', label: 'Montant reçu', field: 'Amount', sortable: true },
        { name: 'Date', align: 'center', label: 'Date de reception', field: 'Date', sortable: true },
        { name: 'Use', align: 'right', label: 'Motif', field: 'Use', sortable: true }
    ]
  export default {
    data () {
      const addDonation = () => {
      var form = new FormData()
      form.append('date', this.date)
      form.append('use', this.use)
      form.append('devise', this.vslaCurrency)
      form.append('amount', this.amount)
      form.append('donor', this.donor)
      form.append('vslaId', this.vslaId)
      if (this.date !== null && this.use !== null && this.amount !== null  && this.donor !== null) {
            axios.post(this.$urlServer + 'donationControllers/addDonation.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Succès',
              message: ('Le don reçu a bien été enregistré'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listdonations()
            })
          })
      } else {
          this.$q.dialog({
            title: 'Erreur',
            message: ('Vous devez compléter tous les champs requis'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          })
        }
      }
      return {
        addDonation,
        columns,
        tab: 'list',
        date: null,
        use: null,
        amount: null,
        donor: null,
        filter: '',
        selected: [],
        donations: [],
        cycleFilter: null,
        cycles: [],
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8),
        vslaCurrency: this.$session.get(10)
      }
    },
    created () {
        this.listCycles()
    },
    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Champs requis')
            }, 800)
            })
        },
        listdonations () {
            axios.get(this.$urlServer + 'donationControllers/listDonation.php', {
                params: {
                  cycleId: this.cycleFilter,
                }
            }).then((dataFront) => {
                this.donations = dataFront.data
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
    }
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    