<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
         <div class="text-h6 text-light-blue-10 text-center">PENALITES ET AMENDES</div>
        </q-card-section>
        <q-separator></q-separator>
        <q-card-section class="bg-grey-1">
          <div class="row flex flex-center">
            <q-item-label class="text-h6 q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
            <div class="col q-pa-xs" align="left">
              <q-select @input="listPenalties" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
                >
              </q-select>
            </div>
            <q-space />
            <div align="right">
              <q-radio keep-color v-model="vslaCurrency" :val="vslaCurrency" :label="vslaCurrency" color="orange" />
            </div>
              
          </div>
        </q-card-section>
        <q-tabs v-model="tab"
            dense
            class="text-grey" style="font-size: 10px;"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink>
          <q-tab name="penalties" no-caps label="Penalités enregistrées" icon="flutter_dash" class="text-negative"/>
          <q-tab name="add" no-caps label="Ajouter une amende" icon="new_label" class="text-accent" />
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="penalties">
            <q-card>
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                title="Penalités et amendes enregistrées"
                :data="penalties"
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
                          <q-item-section side>
                            <q-item-label caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <q-separator></q-separator>
                      <q-card-actions align="around">
                
                        <q-btn side flat no-caps dense icon="edit" color="primary" label="Editer"></q-btn>
                        <q-btn side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn>
                      </q-card-actions>

                     
                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher" class="col">
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
              <q-card-section class="bg-accent text-white">
                <div class="text-subtitle2">Nouvelle amende ou penalité</div>
              </q-card-section>
              <q-separator />
              <q-card-section>
                <q-form class="full-width q-pa-sm">
                  <div class="row q-mb-sm">
                    <q-select dense outlined :options="members" map-options :rules="[myRule]"  v-model="applicantId" ref="applicantId" label="Membre concerné" class="col" emit-value    
                    >
                    </q-select>
                  </div>
                  <div class="row q-mb-sm">
                      <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amount" ref="amount" label="Montant à payer" :rules="[ myRule ]" class="col">
                      </q-input>
                  </div>
                  <div class="row q-mb-sm">
                    <q-select dense outlined :options="uses" :rules="[myRule]"  v-model="infraction" ref="infraction" label="Infraction commise" class="col" emit-value map-options 
                    >
                    </q-select>
                  </div>

                  <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de l'infraction" :rules="[ myRule ]" class="col">
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
                        @click="addPenalty"
                        label="Enregistrer"
                        color="accent"
                        icon="save"
                        outline
                        type="submit"
                      >
                    </q-btn>
                </div>
                </q-form>
              </q-card-section>
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'
  
  const columns = [
        { name: 'value', align: 'right', label: 'ID de la penalité', field: 'value', sortable: true },
        { name: 'Devise', align: 'right', label: 'Devise', field: 'Devise', sortable: true },
        { name: 'Amount',  align: 'right', label: 'Montant à payer', field: 'Amount', sortable: true },
        { name: 'Infraction', align: 'right', label: 'Infraction', field: 'Infraction' },
        { name: 'Date', align: 'right', label: "Date de l'infraction", field: 'Date', sortable: true },
        { name: 'Name', align: 'right', label: 'Membre', field: 'Name', sortable: true },
        
    ]
  export default {
    data () {
      const addPenalty = () => {
      var form = new FormData()
      form.append('amount', this.amount)
      form.append('devise', this.vslaCurrency)
      form.append('infraction', this.infraction)
      form.append('date', this.date)
      form.append('memberId', this.applicantId)
      form.append('vslaId', this.vslaId)
      if (this.amount !== null && this.infraction !== null && this.date !== null) {
        this.$q.dialog({
              title: 'Confirmation',
              message: ('Voulez-vous vraiment enregistrer cette penalité?'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              axios.post(this.$urlServer + 'penalityControllers/addPenality.php', form, {
              }).then((res) => {
                this.$q.dialog({
                  title: 'Success',
                  message: ('La penalité a été enregistrée avec succès'),
                  ok: {
                    push: true
                  },
                  cancel: {
                    color: 'negative'
                  },
                  persistent: true
                }).onOk(() => {
                  this.listPenalties()
                })
              })
            })
          
        } else {
          this.$q.dialog({
            title: 'Erreur',
            message: ('Veuillez compléter tous les champs'),
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
        addPenalty,
        columns,
        tab: 'penalties',
        applicantId: null,
        infraction: null,
        date: null,
        amount: null,
        filter: '',
        selected: [],
        penalties: [],
        members: [],
        cycleFilter: null,
        cycles: [],
        uses: [
          {value: 'Absence aux réunions', label : 'Absence aux réunions'},
          {value: 'Derangement pendant les réunions', label : 'Derangement pendant les réunions'},
          {value: 'Retard dans le paiement', label : 'Retard dans le paiement'},
          {value: 'Autre infraction', label : 'Autre infraction'}
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
      this.listMembers()
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
        listCycles () {
          axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
              params: {
                  vslaId: this.vslaId
              }
          }).then((dataFront) => {
              this.cycles = dataFront.data
          })
        },
        listPenalties () {
            axios.get(this.$urlServer + 'penalityControllers/listPenality.php', {
                params: {
                    cycleId: this.cycleFilter
                }
            }).then((dataFront) => {
                this.penalties = dataFront.data
            })
        },
        listMembers () {
          axios.get(this.$urlServer + 'memberControllers/listMember.php', {
              params: {
                  vslaId: this.vslaId
              }
          }).then((dataFront) => {
              this.members = dataFront.data
          })
        }
    },
    components: {
      'add-loan': require('components/loan/AddLoan.vue').default
    }
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    