<template>
  <q-page class="q-pa-xs">
    <q-card>
      <q-card-section class="bg-grey-1 text-white">
        <div class="text-h6 text-primary text-center">CONTRIBUTIONS SOCIALES</div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section class="bg-grey-1">
        <div class="row flex flex-center">
          <q-item-label class="text-subtitle2 text-bold q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
          <div class="col q-pa-xs" align="left">
            <q-select @input="listSavings" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
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
          narrow-indicator shrink inline-label>
        <q-tab v-if="this.typeUser === 'Cashier'" no-caps name="register" label="Enregistrer un dépôt" icon="sell" class="text-negative" />
        <q-tab no-caps name="saving" label="Voir les contributions" icon="move_to_inbox" class="text-primary"/>
        <!-- <q-tab no-caps name="add" label="Dépôt électronique" icon="send" class="text-accent" /> -->
      </q-tabs>
      <q-separator />
      
      <q-tab-panels v-model="tab">
        <q-tab-panel v-if="this.typeUser === 'Cashier'" name="register">
          <q-card flat>
            <q-card-section class="bg-negative text-white">
              <div class="text-subtitle2">Enregistrer une contibution sociale</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-form class="full-width">
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="members" map-options :rules="[myRule]"  v-model="depositorId" ref="operator" label="Sélectionner le membre déposant" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="['USD', 'CDF']" :rules="[myRule]"  v-model="devisetosave" ref="devisetosave" label="Choisir la devise" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" :prefix="devisetosave" dense outlined v-model="amounttosave" ref="amounttosave" label="Montant déposé" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="['Cash liquide', 'Electronique (Mobile Money)']" :rules="[myRule]"  v-model="depositmod" ref="depositmod" label="Mode de paiement utilisé" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                  <q-input dense outlined aria-required="required" ref="depositdate" mask="####-##-##" v-model="depositdate" label="Date de dépôt" :rules="[ myRule ]" class="col">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy">
                          <q-date v-model="depositdate" mask="YYYY-MM-DD"></q-date>
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
              </div>
              <div align="right">
                  <q-btn dense
                      @click="SaveDeposit"
                      label="Enregistrer"
                      color="negative"
                      icon="collections_bookmark"
                      outline
                      type="submit"
                    >
                  </q-btn>
              </div>
              </q-form>
            </q-card-section>
          </q-card>
        </q-tab-panel>

        <q-tab-panel name="saving">
          <q-card flat>
            <q-card-section class="bg-primary text-white">
              <div class="text-subtitle2">Contributions enregistrées</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-table bordered flat separator="cell"
                  :data="savings"
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
                        <q-card-actions align="around" v-if="typeUser == 'Cashier'">
                          <q-btn  side flat no-caps dense icon="edit" color="primary" label="Editer"></q-btn>
                          <q-btn  side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn>
                        </q-card-actions>
                      </q-card>
                    </div>
                  </template>
    
                  <template v-slot:top-right>
                      <q-input dense filled debounce="500" v-model="filter" placeholder="Rechercher un dépôt" class="col">
                      <template v-slot:append>
                          <q-icon name="search" />
                      </template>
                      </q-input>
                  </template>
    
              </q-table>
            </q-card-section>
        </q-card>
        </q-tab-panel>

        <q-tab-panel name="add">
          <q-card flat>
            <q-card-section class="bg-accent text-white">
              <div class="text-subtitle2">Envoyer une contribution électronique</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
              <q-form class="full-width">
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="['USD', 'CDF']" :rules="[myRule]"  v-model="devise" ref="devise" label="Choisir la devise" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm" v-if="devise != null">
                    <q-input type="number" :prefix="devise" dense outlined v-model="amount" ref="amount" label="Montant à déposer" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm" v-if="amount != null">
                  <q-input dense type="tel" outlined :rules="[myRule]" v-model="merchant" ref="merchant" label="Numéro de téléphone du Groupe" mask="#########" class="col" placeholder="ex. 995656345">
                  </q-input>
                </div>
                <div class="row q-mb-sm" v-if="merchant != null">
                  <q-input dense outlined :rules="[myRule]" v-model="pin" ref="pin" label="Entrez votre PIN" mask="####" class="col">
                  </q-input>
                </div>
  
              <div align="right">
                  <q-btn dense
                      @click="sendMoney"
                      label="Envoyer"
                      color="accent"
                      icon="move_up"
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
      { name: 'value', align: 'left', label: 'Trans ID', field: 'value', sortable: true },
      {
        name: 'Name',
        required: true,
        label: 'Nom du membre',
        align: 'left',
        field: row => row.Name,
        format: val => `${val}`,
        sortable: true
      },
      { name: 'Cycle', align: 'right', label: 'Cycle', field: 'Cycle', sortable: true },
      { name: 'Devise', align: 'right', label: 'Devise', field: 'Devise', sortable: true },
      { name: 'Amount',  align: 'right', label: 'Montant déposé', field: 'Amount', sortable: true },
      { name: 'Mode', align: 'left', label: 'Mode de paiement', field: 'Mode' },
      { name: 'Date', align: 'left', label: 'Date de paiement', field: 'Date' }
      
  ]
export default {
  data () {
    const SaveDeposit = () => {
    var form = new FormData()
    form.append('vslaId', this.$session.get(6))
    form.append('memberId', this.depositorId)
    form.append('type', this.deposittype)
    form.append('devise', this.devisetosave)
    form.append('amount', this.amounttosave)
    form.append('mode', this.depositmod)
    form.append('date', this.depositdate)
    if (this.name !== null && this.depositorId !== null && this.devisetosave !== null && this.amounttosave !== null && this.depositdate !== null && this.depositdate !== null ) {
        axios.post(this.$urlServer + 'depositControllers/addDeposit.php', form, {
        }).then((res) => {
          this.$q.dialog({
            title: 'Success',
            message: ("La contribution sociale a été enregistrée avec succès"),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            this.listSavings()
          })
        })
      } else {
        this.$q.dialog({
          title: 'Error',
          message: ('Fill all required fields please!'),
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
    const sendMoney = () => {
    }
    return {
      SaveDeposit,
      sendMoney,
      columns,
      tab: 'saving',
      devise: null,
      amount: null,
      pin: null,
      merchant: null,
      depositorId: null,
      devisetosave: null,
      amounttosave: null,
      deposittype: 'Contribution sociale',
      depositmod: null,
      depositdate: null,
      filter: '',
      selected: [],
      members: [],
      savings: [],
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
      listSavings () {
          axios.get(this.$urlServer + 'depositControllers/listSocilDeposit.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.savings = dataFront.data
          })
      }
  }
}
</script>

<style lang="sass">
.grid-style-transition
  transition: transform .28s, background-color .28s
</style>
  