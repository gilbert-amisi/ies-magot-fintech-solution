<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
          <div class="text-h6 text-light-blue-10 text-center">PARTAGE DES CAPITAUX</div>
        </q-card-section>
        <q-separator></q-separator>
        <q-tabs v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink inline-label>
          <q-tab v-if="typeUser == 'Cashier'" no-caps name="newshare" label="Enregister un retrait" icon="add_home" class="text-accent" />
          <q-tab no-caps name="list" label="Retraits enregistrés" icon="pix" class="text-primary"/>
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="list">
            <q-card flat>
              <q-card-section class="bg-grey-1">
                <div class="row flex flex-center">
                  <q-item-label class="text-subtitle2 q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
                  <div class="col q-pa-xs" align="left">
                    <q-select @input="listRetraits" class="col" dense standard :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
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
            <q-card-section class="bg-light-blue-10">
              <q-item-label class="text-subtitle2 text-bold text-white">Retraits par les membres au partage</q-item-label>
            </q-card-section>
            <q-separator></q-separator>
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                :data="retraits"
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
                      <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                        <q-btn side flat no-caps dense icon="edit" color="primary" label="Editer"></q-btn>
                        <!-- <q-btn side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn> -->
                      </q-card-actions>

                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input dense outlined debounce="500" v-model="filter" placeholder="Rechercher un retrait" class="col">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                    </q-input>
                </template>
  
            </q-table>
            </q-card>
          </q-tab-panel>

          <q-tab-panel name="newshare" v-if="typeUser == 'Cashier'">
            <q-card flat>
              <q-card-section class="bg-accent">
                <q-item-label class="text-subtitle2 text-bold text-white">Nouveau retrait au partage</q-item-label>
              </q-card-section>
              <q-separator></q-separator>
              <q-form class="full-width q-pa-sm">
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="members" map-options :rules="[myRule]"  v-model="receiverId" ref="receiverId" label="Membre concerné" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amount" ref="amount" label="Montant retiré" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de partage" :rules="[ myRule ]" class="col">
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
                      @click="addRetrait"
                      label="Enregistrer"
                      color="accent"
                      icon="save_as"
                      outline
                      type="submit"
                    >
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
        { name: 'Amount',  align: 'right', label: 'Montant retiré', field: 'Amount', sortable: true },
        { name: 'Date', align: 'right', label: 'Date de retrait', field: 'Date', sortable: true },
        { name: 'Name', align: 'right', label: 'Nom du membre', field: 'Name' }
        
    ]
  export default {
    data () {
      const addRetrait = () => {
      var form = new FormData()
      form.append('amount', this.amount)
      form.append('date', this.date)
      form.append('memberId', this.receiverId)
      form.append('vslaId', this.vslaId)
      if (this.amount !== null && this.receiverId !== null && this.date !== null) {
        this.$q.dialog({
              title: 'Confirmation',
              message: ('Voulez-vous vraiment enregistrer ce retrait?'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              axios.post(this.$urlServer + 'shareout/addShareout.php', form, {
              }).then((res) => {
                this.$q.dialog({
                  title: 'Notification',
                  message: ('Le retrait du capital a été enregistré avec succès'),
                  ok: {
                    push: true
                  },
                  cancel: {
                    color: 'negative'
                  },
                  persistent: true
                }).onOk(() => {
                  this.listRetraits()
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
        addRetrait,
        columns,
        tab: 'list',
        receiverId: null,
        amount: null,
        date: null,
        filter: '',
        selected: [],
        retraits: [],
        members: [],
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
        listRetraits () {
            axios.get(this.$urlServer + 'shareout/listShareout.php', {
                params: {
                  cycleId: this.cycleFilter,
                }
            }).then((dataFront) => {
                this.retraits = dataFront.data
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
    