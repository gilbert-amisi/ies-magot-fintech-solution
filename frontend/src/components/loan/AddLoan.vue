<template>
    <q-card style="width: 100%;">
      <q-card-section class="bg-grey-1">
        <div class="row">
            <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Enregistrer un crédit octroyé</q-item-label>
            <q-space></q-space>
            <q-btn flat round dense icon="close" v-close-popup></q-btn>
        </div>
        <q-separator></q-separator>
        <div class="row no-wrap q-pa-sm">
            <div class="column">
                <div class="text-h6 q-mb-sm;">Détails de la demande</div>
                    <q-toggle v-model="settoggle">N° de la demande : {{ requestId }}</q-toggle>
                    <q-toggle v-model="settoggle">Devise du groupe : {{ vslaCurrency }}</q-toggle>
                    <q-toggle v-model="settoggle">Montant validé : {{ validatedAmount }}</q-toggle>
                </div>

                <q-separator vertical inset class="q-mx-sm" />

                <div class="column items-center">
                <q-avatar size="70px">
                    <img src="https://cdn.quasar.dev/img/boy-avatar.png">
                </q-avatar>

                <div class="text-subtitle1 text-bold q-mt-sm q-mb-xs">{{ memberName }}</div>
                <div class="text-subtitle1 q-mt-sm q-mb-xs">{{ phoneNumber }}</div>

            </div>
        </div>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
          <div class="row q-mb-sm">
              <q-input type="number" :prefix="vslaCurrency" dense outlined v-model="amounttosave" ref="amounttosave" label="Montant accordé" :rules="[ myRule ]" class="col">
              </q-input>
          </div>
          <div class="row q-mb-sm">
            <q-input dense outlined aria-required="required" ref="startdate" mask="####-##-##" v-model="startdate" label="Date de debut" :rules="[ myRule ]" class="col">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy">
                    <q-date v-model="startdate" mask="YYYY-MM-DD"></q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div class="row q-mb-sm">
            <q-input dense outlined aria-required="required" ref="enddate" mask="####-##-##" v-model="enddate" label="Date de fin" :rules="[ myRule ]" class="col">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy">
                    <q-date v-model="enddate" mask="YYYY-MM-DD"></q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div class="row q-mb-sm">
              <q-input type="number" prefix="%" dense outlined v-model="rate" ref="rate" label="Taux d'intérêt" :rules="[ myRule ]" class="col">
              </q-input>
          </div>
          <div align="right">
            <q-btn dense
              @click="SaveLoan"
              label="Enregistrer"
              color="light-blue-10"
              icon="collections_bookmark"
              outline
              type="submit"
              >
            </q-btn>
          </div>
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
    props: ['requestId', 'phoneNumber', 'memberName', 'validatedAmount', 'vslaCurrency'],
    data () {
        const SaveLoan = () => {
            var form = new FormData()
            form.append('requestId', this.requestId)
            form.append('devise', this.vslaCurrency)
            form.append('amount', this.amounttosave)
            form.append('rate', this.rate)
            form.append('start', this.startdate)
            form.append('end', this.enddate)
            form.append('status', this.status)
            if (this.amounttosave !== null && this.startdate !== null && this.enddate !== null && this.rate !== null ) {
              this.$q.dialog({
                title: 'Confirmation',
                message: ("Voulez-vous vraiment enregistré ce crédit? Notez que l'intérêt sera coupé à la source et enregistré en même temps."),
                ok: {
                push: true
                },
                cancel: {
                  color: 'negative'
                },
                persistent: true
              }).onOk(() => {
                axios.post(this.$urlServer + 'loanControllers/addLoan.php', form, {
                }).then((res) => {
                this.$q.dialog({
                  title: 'Notification',
                  message: ("Le crédit octroyé et l'intérêt versé à la source ont été enregistrés avec succès"),
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
              })
                
            } else {
                this.$q.dialog({
                title: 'Erreur',
                message: ('veuillez compléter tous les champs!'),
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
        SaveLoan,
        settoggle: false,
        amounttosave: null,
        startdate: null,
        enddate: null,
        rate: null,
        status: 'Encours',
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
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
  