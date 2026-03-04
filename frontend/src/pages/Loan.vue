<template>
  <q-page class="q-pa-xs">
    <q-card>
      <q-card-section class="bg-grey-1 text-white">
        <div class="text-h6 text-light-blue-10 text-center">CREDITS OCTROYES</div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section class="bg-grey-1">
        <div class="row flex flex-center">
          <q-item-label class="text-subtitle2 text-bold q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
          <div class="col q-pa-xs" align="left">
            <q-select @input="listAll" class="col" dense standard :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
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
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator shrink>
        <q-tab no-caps name="pending" label="Crédits sains encours" icon="pending" class="text-teal"/>
        <q-tab no-caps name="suffering" label="Crédits en souffrance" icon="warning" class="text-negative"/>
        <q-tab no-caps name="complete" label="Crédits clôturés" icon="battery_charging_full" class="text-primary" />
        <q-tab no-caps name="disbursements" label="Détails Remboursements" icon="payments" class="text-accent" />
      </q-tabs>
      <q-separator />
      
      <q-tab-panels v-model="tab">
        <q-tab-panel name="complete">
          <q-card flat>
            <q-card-section class="bg-primary text-white">
              <div class="text-subtitle2">Crédits totalement remboursés </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-table bordered flat separator="cell"
                  :data="cloloans"
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
                        <q-card-section class="text-subtitle2 bg-light-blue-1">
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
                        <q-separator />
                        <!-- <q-card-actions align="around">
                          <q-btn side flat no-caps dense color="primary" icon="edit" label="Editer"></q-btn>
                          <q-btn side flat no-caps dense color="negative" icon="delete" label="Supprimer"></q-btn>
                        </q-card-actions> -->
                      </q-card>
                    </div>
                  </template>
    
                  <template v-slot:top-right>
                      <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un crédit" class="col">
                      <template v-slot:append>
                          <q-icon name="search" />
                      </template>
                      </q-input>
                  </template>
    
              </q-table>
            </q-card-section>
          </q-card>
        </q-tab-panel>

        <q-tab-panel name="pending">
          <q-card flat>
            <q-card-section class="bg-teal-5 text-white">
              <div class="text-subtitle2">Crédits sains encours</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-table bordered flat separator="cell"
                  :data="loans"
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
                        <q-card-section class="text-subtitle2 bg-light-blue-1" v-if="props.row.Echeance > 0 ">
                          <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                        </q-card-section>
                        <q-card-section class="text-subtitle2 bg-red-1" v-if="props.row.Echeance < 0 ">
                          <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                        </q-card-section>
                        <q-card-section class="text-subtitle2 bg-amber-1" v-if="props.row.Echeance == 0 ">
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
                        <q-separator />
                        <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                          <q-btn @click="addPayment(props.row.value, props.row.Cycle, props.row.Name, props.row.Net, props.row.Devise)" side flat no-caps dense color="purple" label="Paiement"></q-btn>
                          <q-btn  side flat no-caps dense color="primary" label="Editer"></q-btn>
                          <!-- <q-btn  side flat no-caps dense color="negative" label="Supprimer"></q-btn> -->
                        </q-card-actions>
                      </q-card>
                    </div>
                  </template>
    
                  <template v-slot:top-right>
                      <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un crédit" class="col">
                      <template v-slot:append>
                          <q-icon name="search" />
                      </template>
                      </q-input>
                  </template>
    
              </q-table>
            </q-card-section>
          </q-card>
          <q-dialog v-model="showAddPayment" persistent>
            <add-payment :creditId="creditId" :cycleId="cycleId" :debtor="debtor" :dueAmount="dueAmount" :currency="currency"></add-payment>
          </q-dialog>
        </q-tab-panel>

        <q-tab-panel name="suffering">
          <q-card flat>
            <q-card-section class="bg-negative text-white">
              <div class="text-subtitle2">Crédits en souffrance</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-table bordered flat separator="cell"
                  :data="overloans"
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
                        <q-card-section class="text-subtitle2 bg-light-blue-1" v-if="props.row.Echeance > 0 ">
                          <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                        </q-card-section>
                        <q-card-section class="text-subtitle2 bg-red-1" v-if="props.row.Echeance < 0 ">
                          <q-checkbox dense v-model="props.selected" :label="props.row.Name" />
                        </q-card-section>
                        <q-card-section class="text-subtitle2 bg-amber-1" v-if="props.row.Echeance == 0 ">
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
                        <q-separator />
                        <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                          <q-btn @click="addPayment(props.row.value, props.row.Cycle, props.row.Name, props.row.Net, props.row.Devise)" side flat no-caps dense color="purple" label="Paiement"></q-btn>
                          <q-btn  side flat no-caps dense color="primary" label="Editer"></q-btn>
                          <!-- <q-btn  side flat no-caps dense color="negative" label="Supprimer"></q-btn> -->
                        </q-card-actions>
                      </q-card>
                    </div>
                  </template>
    
                  <template v-slot:top-right>
                      <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un crédit" class="col">
                      <template v-slot:append>
                          <q-icon name="search" />
                      </template>
                      </q-input>
                  </template>
    
              </q-table>
            </q-card-section>
          </q-card>
          <q-dialog v-model="showAddPayment" persistent>
            <add-payment :creditId="creditId" :cycleId="cycleId" :debtor="debtor" :dueAmount="dueAmount" :currency="currency"></add-payment>
          </q-dialog>
        </q-tab-panel>

        <q-tab-panel name="disbursements">
          <q-card flat>
            <q-card-section class="bg-accent text-white">
              <div class="text-subtitle2">Détails des remboursements</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-table bordered flat separator="cell"
                  :data="refunds"
                  :columns="columns2"
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
                        <q-card-section class="text-subtitle2 bg-purple-1">
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
                        <q-separator />
                        <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                          <q-btn icon="edit" side flat no-caps dense color="primary" label="Editer"></q-btn>
                          <!-- <q-btn icon="delete" side flat no-caps dense color="negative" label="Supprimer"></q-btn> -->
                        </q-card-actions>
                      </q-card>
                    </div>
                  </template>
    
                  <template v-slot:top-right>
                      <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un remboursement" class="col">
                      <template v-slot:append>
                          <q-icon name="search" />
                      </template>
                      </q-input>
                  </template>
    
              </q-table>
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
    { name: 'value', align: 'left', label: 'Crédit ID', field: 'value', sortable: true },
    { name: 'Amount',  align: 'right', label: 'Capital prêté ', field: 'Amount', sortable: true },
    { name: 'Interet',  align: 'right', label: 'Interêt', field: 'Interet', sortable: true },
    { name: 'Remboursement',  align: 'right', label: 'Total remboursé', field: 'Remboursement', sortable: true },
    { name: 'Reste', align: 'left', label: 'Reste à payer', field: 'Reste' },
    { name: 'Echeance', align: 'left', label: 'Echéance (jours)', field: 'Echeance' },
    { name: 'Name', required: true, label: 'Membre', align: 'left', field: row => row.Name, format: val => `${val}`, sortable: true },
    { name: 'Cycle', align: 'right', label: 'Cycle', field: 'Cycle', sortable: true }  
  ]

  const columns2 = [
    { name: 'value', align: 'left', label: 'Trans ID', field: 'value', sortable: true },
    { name: 'Devise', align: 'left', label: 'Devise', field: 'Devise', sortable: true },
    { name: 'Amount',  align: 'right', label: 'Montant payé ', field: 'Amount', sortable: true },
    { name: 'Date', align: 'left', label: 'Date de paiement', field: 'Date' },
    { name: 'Name', required: true, label: 'Membre', align: 'left', field: row => row.Name, format: val => `${val}`, sortable: true },
    { name: 'Credit', align: 'left', label: 'Crédit ID', field: 'Credit', sortable: true }, 
  ]
export default {
  data () {


    return {
      columns,
      columns2,
      tab: 'pending',
      status: 'Encours',
      filter: '',
      selected: [],
      loans: [],
      overloans: [],
      cloloans: [],
      refunds: [],
      cycleFilter: null,
      cycles: [],
      showAddPayment: false,
      creditId: null,
      cycleId: null,
      debtor: null,
      dueAmount: null,
      currency: null,
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
      listCycles () {
        axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
            params: {
                vslaId: this.vslaId
            }
        }).then((dataFront) => {
            this.cycles = dataFront.data
        })
      },
      listLoans () {
          axios.get(this.$urlServer + 'loanControllers/listLoan.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.loans = dataFront.data
          })
      },
      listoverLoans () {
          axios.get(this.$urlServer + 'loanControllers/listLoanSouffrance.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.overloans = dataFront.data
          })
      },
      listCloLoans () {
          axios.get(this.$urlServer + 'loanControllers/listClosedLoan.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.cloloans = dataFront.data
          })
      },
      listRefunds () {
          axios.get(this.$urlServer + 'refundControllers/listRefund.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.refunds = dataFront.data
          })
      },
      listAll () {
        this.listLoans()
        this.listoverLoans()
        this.listCloLoans()
        this.listRefunds()
      },
      addPayment (creditId, cycleId, debtor, dueAmount, currency) {
        this.creditId = creditId
        this.cycleId = cycleId
        this.debtor = debtor
        this.dueAmount = dueAmount
        this.currency = currency
        this.showAddPayment = true
      }
  },
  components: {
    'add-payment': require('components/loan/AddPayment.vue').default
  }
}
</script>

<style lang="sass">
.grid-style-transition
  transition: transform .28s, background-color .28s
</style>
  