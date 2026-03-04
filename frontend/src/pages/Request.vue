<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
        <div class="text-h6 text-light-blue-10 text-center">DEMANDES DE CREDITS</div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section class="bg-grey-1">
        <div class="row flex flex-center">
          <q-item-label class="text-subtitle2 text-bold q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
          <div class="col q-pa-xs" align="left">
            <q-select @input="listAll" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
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
          <q-tab name="requests" no-caps label="Demandes en attente" icon="pending_actions" class="text-negative"/>
          <q-tab name="validations" no-caps label="Demandes validées" icon="checklist" class="text-light-blue-10"/>
          <q-tab name="add" no-caps label="Initier une demande" icon="new_label" class="text-accent" />
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="requests">
            <q-card>
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                title="Demandes de crédits en attente"
                :data="requests"
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
                        <q-btn v-if="typeUser == 'Chairman' && props.row.Status == 'Validation 2'" @click="prepareValidation(props.row.value, 'Validation 3', props.row.Phone, props.row.Name, props.row.Operator)" side flat no-caps dense icon="check" color="primary" label="Valider"></q-btn>
                        <q-btn v-if="typeUser == 'Secretary' && props.row.Status == 'Validation 1'" @click="validateRequest(props.row.value, 'Validation 2', props.row.Amount, props.row.Phone, props.row.Name, props.row.memberId)" side flat no-caps dense icon="check" color="primary" label="Valider"></q-btn>
                        <q-btn v-if="typeUser == 'Cashier' && props.row.Status == 'En attente'" @click="validateRequest(props.row.value, 'Validation 1', props.row.Amount, props.row.Phone, props.row.Name, props.row.memberId)" side flat no-caps dense icon="check" color="primary" label="Valider"></q-btn>
                        <q-btn v-if="typeUser == 'Cashier' && props.row.Status == 'En attente'" @click="refuseRequest(props.row.value, 'Refusée', props.row.Amount, props.row.Phone, props.row.Name)" side flat no-caps dense icon="close" color="accent" label="Refuser"></q-btn>
                        <q-btn v-if="typeUser == 'Secretary' && props.row.Status == 'Validation 1'" @click="refuseRequest(props.row.value, 'Refusée', props.row.Amount, props.row.Phone, props.row.Name)" side flat no-caps dense icon="close" color="accent" label="Refuser"></q-btn>
                        <q-btn v-if="typeUser == 'Chairman' && props.row.Status == 'Validation 2'" @click="refuseRequest(props.row.value, 'Refusée', props.row.Amount, props.row.Phone, props.row.Name)" side flat no-caps dense icon="close" color="accent" label="Refuser"></q-btn>
                        <q-btn v-if="memberId == props.row.memberId && props.row.Status == 'En attente'" @click="deleteRequest(props.row.value)" side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn>
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
            <q-dialog v-model="validation" persistent>
              <q-card>
                <q-bar class="bg-grey-2">
                  <div class="text-bold tex-h6">Validation de la demande</div>

                  <q-space />

                  <q-btn dense flat icon="close" v-close-popup>
                    <q-tooltip>Fermer</q-tooltip>
                  </q-btn>
                </q-bar>

                <q-card-section class="text-center">
                  Entrez le montant validé pour la demande du crédit de <b>{{ this.memberName }} </b>
                  <!-- Entrez le montant et le Code PIN du compte <b>{{ this.groupAccount }}</b> du groupe <br /> pour envoyer du crédit au  <b> {{ this.phoneNumber }} ({{ this.memberName }})</b> -->
                </q-card-section>

                <q-card-section class="q-pt-none">
                  <q-form class="full-width q-pa-sm" v-if="typeUser == 'Chairman'">
                    <div class="row q-mb-sm">
                        <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="validatedAmount" ref="validatedAmount" label="Montant validé" :rules="[ myRule ]" class="col">
                        </q-input>
                    </div>
                    <!-- <div class="row q-mb-sm">
                      <q-input dense v-model="pin" label="Code PIN" ref="pin" class="col" :rules="[ myRule ]"
                        :type="isPwd ? 'password' : 'number'" outlined>
                        <template v-slot:prepend>
                          <q-icon name="lock" class="cursor-pointer"></q-icon>
                        </template>
                        <template v-slot:append>
                          <q-icon :name="isPwd ? 'visibility_off' : 'visibility'"
                            class="cursor-pointer" @click="isPwd = !isPwd"
                          />
                        </template>
                      </q-input>
                    </div> -->
                    
      
                    <div align="right">
                        <q-btn dense
                            @click="completeValidation"
                            label="Confirmer"
                            color="primary"
                            icon="check"
                            outline
                            type="submit"
                          >
                        </q-btn>
                    </div>
                  </q-form>
                </q-card-section>
              </q-card>
            </q-dialog>

          </q-card>
          </q-tab-panel>

          <q-tab-panel name="validations">
            <q-card>
              <q-card-section class="bg-primary text-white">
                <div class="text-subtitle2">Demandes complètement validées </div>
              </q-card-section>
              <q-separator />
              <q-card-section>
                <q-table bordered flat separator="cell" class="col q-pa-sm"
                  :data="valrequests"
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
                        <q-card-actions align="around" v-if="props.row.Status=='Validation complete'">
                          <q-btn v-if="typeUser == 'Cashier'" @click="octroyerCredit(props.row.value, props.row.Phone, props.row.Name, props.row.Validateamount)" side push no-caps dense icon="send_and_archive" color="light-blue-10" label="Enregistrer le crédit octroyé"></q-btn>
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
            </q-card-section>
            <q-dialog v-model="octroi" persistent>
              <add-loan :requestId="requestId" :phoneNumber="phoneNumber" :memberName="memberName" :validatedAmount="validatedAmount" :vslaCurrency="vslaCurrency"></add-loan>
            </q-dialog>

          </q-card>
          </q-tab-panel>

          <q-tab-panel name="add">
            <q-card flat>
              <q-card-section class="bg-accent text-white">
                <div class="text-subtitle2">Nouvelle demande de crédit </div>
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
                      <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amount" ref="amount" label="Montant sollicité" :rules="[ myRule ]" class="col">
                      </q-input>
                  </div>
                  <div class="row q-mb-sm">
                    <q-select dense outlined :options="uses" :rules="[myRule]"  v-model="use" ref="use" label="Affectation/Utilisation du crédit" class="col" emit-value map-options 
                    >
                    </q-select>
                  </div>
    
                <div align="right">
                    <q-btn dense no-caps
                        @click="addRequest"
                        label="Soumettre la demande"
                        color="primary"
                        icon="send_time_extension"
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
        { name: 'value', align: 'right', label: 'ID de la demande', field: 'value', sortable: true },
        { name: 'Name', align: 'right', label: 'Demandeur', field: 'Name', sortable: true },
        { name: 'Date', align: 'right', label: 'Date de soumission', field: 'Date', sortable: true },
        { name: 'Amount',  align: 'right', label: 'Montant demandé', field: 'Amount', sortable: true },
        { name: 'Validateamount',  align: 'right', label: 'Montant validé', field: 'Validateamount', sortable: true },
        { name: 'Phone',  align: 'right', label: 'Numéro de téléphone', field: 'Phone', sortable: true },
        { name: 'Operator',  align: 'right', label: 'Opérateur mobile', field: 'Operator', sortable: true },
        { name: 'Status', align: 'right', label: 'Statut de la demande', field: 'Status' }
        
    ]
  export default {
    data () {
      const addRequest = () => {
      var form = new FormData()
      form.append('amount', this.amount)
      form.append('use', this.use)
      form.append('memberId', this.applicantId)
      form.append('vslaId', this.vslaId)
      if (this.amount !== null && this.use !== null) {
        this.$q.dialog({
              title: 'Confirmation',
              message: ('Voulez-vous vraiment soumettre votre demande de crédit?'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              axios.post(this.$urlServer + 'creditRequestControllers/addCreditRequest.php', form, {
              }).then((res) => {
                this.$q.dialog({
                  title: 'Success',
                  message: ('Votre demande de crédit a été envoyée avec succès'),
                  ok: {
                    push: true
                  },
                  cancel: {
                    color: 'negative'
                  },
                  persistent: true
                }).onOk(() => {
                  this.listRequests()
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
        addRequest,
        columns,
        tab: 'requests',
        validation: false,
        octroi: false,
        requestId: null,
        resquestStatus: null,
        phoneNumber: null,
        memberName: null,
        validatedAmount : 0,
        applicantId: null,
        pin : null,
        isPwd: true,
        groupAccount: null,
        use: null,
        amount: null,
        filter: '',
        selected: [],
        requests: [],
        valrequests: [],
        members: [],
        cycleFilter: null,
        seuil: null,
        disponible: null,
        plafond: null,
        fondMembre: 15000,
        cycles: [],
        uses: [
          {value: 'Entrepreneuriat', label : 'Entrepreneuriat'},
          {value: 'AGR', label : 'Activité Génératrice de Revenu'},
          {value: 'Santé', label : 'Soins de santé'},
          {value: 'Education', label : 'Education'},
          {value: 'Construction', label : 'Construction'},
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
        listRequests () {
          axios.get(this.$urlServer + 'creditRequestControllers/listCreditRquest.php', {
            params: {
                cycleId: this.cycleFilter
            }
          }).then((dataFront) => {
              this.requests = dataFront.data
          })
        },
        listValRequests () {
          axios.get(this.$urlServer + 'creditRequestControllers/listValidateRequest.php', {
              params: {
                cycleId: this.cycleFilter
              }
          }).then((dataFront) => {
              this.valrequests = dataFront.data
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
        prepareValidation (id, status, phone, name, operator) {
          this.validation = true,
          this.requestId = id,
          this.resquestStatus = status,
          this.phoneNumber = phone,
          this.memberName = name,
          this.groupAccount = operator
        },
        octroyerCredit (id, phone, name, amount) {
          this.requestId = id,
          this.validatedAmount = amount,
          this.phoneNumber = phone,
          this.memberName = name,
          this.octroi = true
        },
        checkDispo () {
            axios.get(this.$urlServer + 'dashboardControllers/rapportFinancierDash.php', {
                params: {
                  cycleId: this.cycleFilter
                }
            }).then((dataFront) => {
              this.seuil = dataFront.data[0].Seuil
              this.disponible = dataFront.data[0].Disponible
              this.plafond = dataFront.data[0].CreditRate
            }) 
        },


        validateRequest (id, status, amount, phone, name, memberId) {
          axios.get(this.$urlServer + 'creditRequestControllers/fondDispoMembre.php', {
            params: {
              cycleId: this.cycleFilter,
              memberId: memberId
            }
          }).then((dataFront) => {
            this.fondMembre = dataFront.data[0].DisponibleMembre

            if ((this.fondMembre * this.plafond / 100) >= amount) {
              if ((this.disponible - this.seuil) >= amount) {
                this.$q.dialog({
                  title: 'Confirmation',
                  message: 'Êtes-vous sûr de vouloir valider cette demande de crédit : N° ID '+ id +' de ' + amount +' ' + this.vslaCurrency + ' pour ' + name + ' (' + phone + ') ?',
                  ok: {
                      push: true
                  },
                  cancel: {
                      color: 'negative'
                  },
                  persistent: true
                }).onOk(() => {
                  axios.get(this.$urlServer + 'creditRequestControllers/updateStatus.php', {
                    params: {
                      requestId: id,
                      status: status,
                      valAmount: amount
                    } 
                  }).then((dataFront) => {
                    this.$q.dialog({
                        title: 'Notification',
                        message: 'La demande du crédit a été validée et soumise au niveau supérieur !',
                        ok: {
                        push: true
                        },
                        persistent: true
                    })
                    this.listRequests()
                  })
                })
              }else {
                this.$q.dialog({
                  title: 'Solde insuffisant !!!',
                  message: 'Le montant disponible en caisse est insuffisant pour cette demande',
                  ok: {
                      push: true
                  },
                  cancel: {
                      color: 'negative'
                  },
                  persistent: true
                })
              }
            } else {
              this.$q.dialog({
                title: 'Plafond atteint !!!',
                message: 'Le montant demandé par le membre depasse sa capacité de paiement',
                ok: {
                    push: true
                },
                cancel: {
                    color: 'negative'
                },
                persistent: true
              })
            }
          }) 
          
        },


        completeValidation () {
          this.$q.dialog({
            title: 'Confirmation',
            message: 'Êtes-vous sûr de vouloir valider cette demande de crédit : N° ID'+ this.requestId +' de ' + this.memberName + 'pour un montant de ' + this.validatedAmount + this.vslaCurrency + ' ?',
            ok: {
                push: true
            },
            cancel: {
                color: 'negative'
            },
            persistent: true
        }).onOk(() => {
            axios.get(this.$urlServer + 'creditRequestControllers/updateStatus.php', {
              params: {
                requestId: this.requestId,
                status: 'Validation complete',
                valAmount: this.validatedAmount
              } 
            }).then((dataFront) => {
            this.$q.dialog({
                title: 'Notification',
                message: 'La demande du crédit a été validée par le dernier niveau',
                ok: {
                push: true
                },
                persistent: true
            })
            this.listRequests()
            })
        })
        },
        refuseRequest (id, status, amount, phone, name) {
          this.$q.dialog({
            title: 'Refus de la demande',
            message: 'Êtes-vous sûr de vouloir refuser cette demande de crédit : N° ID '+ id +' de ' + amount +' ' + this.vslaCurrency + ' pour ' + name + ' (' + phone + ') ?',
            ok: {
                push: true
            },
            cancel: {
                color: 'negative'
            },
            persistent: true
        }).onOk(() => {
            axios.get(this.$urlServer + 'creditRequestControllers/updateStatus.php', {
              params: {
                requestId: id,
                status: status,
                valamount: this.validatedAmount
              } 
            }).then((dataFront) => {
            this.$q.dialog({
                title: 'Notification',
                message: 'La demande du crédit a été refusée !',
                ok: {
                push: true
                },
                persistent: true
            })
            this.listRequests()
            })
        })
      },
      deleteRequest (id) {
        this.$q.dialog({
          title: 'Confirmation',
          message: 'Voulez-vous vraiment supprimer votre demande de crédit?',
          ok: {
              push: true
          },
          cancel: {
              color: 'negative'
          },
          persistent: true
        }).onOk(() => {
          axios.get(this.$urlServer + 'creditRequestControllers/deleteRequest.php', {
            params: {
              requestId: id
            }   
          }).then((dataFront) => {
              this.$q.dialog({
                  title: 'Notification',
                  message: 'Demande de crédit supprimé avec succès !',
                  ok: {
                  push: true
                  },
                  persistent: true
              })
              this.listRequests()
              })
          })
      },
      listAll () {
        this.listRequests()
        this.listValRequests()
        this.checkDispo()
      },
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
    