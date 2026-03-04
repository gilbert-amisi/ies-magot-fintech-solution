<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-tabs v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink>
          <q-tab no-caps name="list" label="Dépenses supportées" icon="how_to_vote" class="text-primary"/>
          <q-tab no-caps v-if="typeUser == 'Chairman' || typeUser == 'Secretary' || typeUser == 'Cashier'" name="add" label="Enregistrer une dépense" icon="library_add" class="text-accent" />
          
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="list">
            <q-card flat>
              <q-card-section class="bg-grey-1">
                <div class="row flex flex-center">
                  <q-item-label class="text-h6 q-pa-sm text-light-blue-10">Filtrer par</q-item-label>
                  <div class="col q-pa-xs" align="left">
                    <q-select @input="listSpendings" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" label="Cycle" map-options emit-value    
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
  
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                title="Dépenses du groupe"
                :data="spendings"
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
                        <q-checkbox dense v-model="props.selected" :label="props.row.Use" />
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
                      <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                        <q-btn side flat no-caps dense icon="edit" color="primary" label="Modifier"></q-btn>
                        <q-btn side flat no-caps dense icon="close" color="negative" label="Annuler"></q-btn>
                      </q-card-actions>
                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input outlined dense debounce="500" v-model="filter" placeholder="Rechercher une depense" class="col">
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
                <q-item-label class="text-subtitle2 text-bold text-white">Enregistrement de la dépense</q-item-label>
              </q-card-section>
              <q-separator></q-separator>
              <q-form class="full-width q-pa-sm">
                <div class="row q-mb-sm">
                    <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amount" ref="amount" label="Montant dépensé" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="uses" :rules="[myRule]"  v-model="use" ref="use" label="Utilisation de l'argent dépensé" class="col" emit-value map-options 
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                  <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de dépense" :rules="[ myRule ]" class="col">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy">
                          <q-date v-model="date" mask="YYYY-MM-DD"></q-date>
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                </div>
                <div align="right">
                  <q-btn dense
                      @click="addSpending"
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
        { name: 'value', align: 'right', label: 'Trans ID', field: 'value', sortable: true },
        { name: 'Date', align: 'center', label: 'Date de dépense', field: 'Date', sortable: true },
        { name: 'Amount',  align: 'right', label: 'Montant dépensé', field: 'Amount', sortable: true },
        { name: 'Use',  align: 'right', label: 'Libellé', field: 'Use', sortable: true },
    ]
  export default {
    data () {
      const addSpending = () => {
      var form = new FormData()
      form.append('date', this.date)
      form.append('use', this.use)
      form.append('amount', this.amount)
      form.append('devise', this.vslaCurrency)
      form.append('vslaId', this.vslaId)
      if (this.date !== null && this.use !== null && this.amount !== null) {
            axios.post(this.$urlServer + 'spendingControllers/addSpending.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Succès',
              message: ('La dépense a bien été enregistrée'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listSpendings()
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
        addSpending,
        columns,
        tab: 'list',
        date: null,
        use: null,
        amount: null,
        donor: null,
        filter: '',
        selected: [],
        spendings: [],
        cycleFilter: null,
        cycles: [],
        uses: [
          {value: 'Assistance sociale', label : 'Assistance sociale'},
          {value: "Création de l'AGR", label : "Création de l'AGR"},
          {value: 'Activité culturelle', label : 'Activité culturelle (fête)'},
          {value: 'Fonctionnement du bureau', label : 'Fonctionnement du bureau'},
          {value: 'Autre', label : 'Autre utilisation'}
        ],
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
        listSpendings () {
            axios.get(this.$urlServer + 'spendingControllers/listSpending.php', {
                params: {
                  cycleId: this.cycleFilter,
                }
            }).then((dataFront) => {
                this.spendings = dataFront.data
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
        }
    }
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    