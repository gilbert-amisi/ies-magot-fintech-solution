<template>
    <q-card style="width: 100%;">
      <q-card-section class="bg-grey-1">
        <div class="row">
            <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Enregistrer un paiement</q-item-label>
            <q-space></q-space>
            <q-btn flat round dense icon="close" v-close-popup></q-btn>
        </div>
        <q-separator></q-separator>
        <div class="row no-wrap q-pa-sm">
            <div class="column">
                <div class="text-h6 q-mb-sm;">Détails</div>
                    <q-toggle v-model="settoggle">Cycle : {{ cycleId }}</q-toggle>
                    <q-toggle v-model="settoggle">Crédit ID : {{ creditId }}</q-toggle>
                </div>

                <q-separator vertical inset class="q-mx-sm" />

                <div class="column items-center">
                <q-avatar size="64px">
                    <img src="https://cdn.quasar.dev/img/boy-avatar.png">
                </q-avatar>

                <div class="text-subtitle1 q-mt-sm q-mb-xs">{{ debtor }}</div>
                <div class="text-subtitle1 q-mt-sm q-mb-xs">Reste à payer : {{ dueAmount }} {{ currency }}</div>

            </div>
        </div>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
          <q-card-section>
            <div class="q-gutter-sm">
                <div class="row q-mb-sm">
                    <q-input type="number" :prefix="currency" dense outlined v-model="amount" :rules="[myRule]" ref="amount" label="Montant payé" class="col"></q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de paiement" :rules="[ myRule ]" class="col">
                    <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy">
                            <q-date v-model="date" mask="YYYY-MM-DD"></q-date>
                        </q-popup-proxy>
                        </q-icon>
                    </template>
                    </q-input>
                </div>
            </div>
              
            <div align="right">
                <q-btn
                    @click="savePayment"
                    label="Enregistrer"
                    color="primary"
                    icon="save"
                    outline
                    type="submit">
                </q-btn>
            </div>
          </q-card-section>
        </q-form>
      </q-card-section>
    </q-card>
  </template>
  <script>
  import vue from 'vue'
  import axios from 'axios'
  import mysession from 'vue-session'
  vue.use(mysession)
  export default {
    props: ['creditId', 'cycleId', 'debtor', 'dueAmount', 'currency'],
    data () {
      const savePayment = () => {
      var form = new FormData()
      form.append('amount', this.amount)
      form.append('date', this.date)
      form.append('loanId', this.creditId)
      if (this.amount !== null && this.date !== null) {
        if (this.amount <= this.dueAmount) {
            axios.post(this.$urlServer + 'refundControllers/addRefund.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Succès',
              message: 'Le paiement du crédit a bien été enregistré',
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.$router.push('/loan')
            })
          })
        } else {
            this.$q.dialog({
            website: 'Erreur',
            message: ('Le montant payé ne doit pas dépasser le net à payer!'),
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
            website: 'Erreur',
            message: ('Veuillez remplir tous les champs!'),
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
        savePayment,
        settoggle: false,
        amount: null,
        date: null,
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3)
      }
    },
    methods: {
      myRule (val) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            resolve(!!val || '* Required')
          }, 1000)
        })
      }
    }
  }
  </script>
  