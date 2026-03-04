<template>
    <q-page class="q-pa-sm">
      <q-card flat v-if="typeUser == 'Chairman' || typeUser == 'Secretary'">
        <q-card-section class="bg-light-blue-10 text-white">
          <div class="text-subtitle2">Nouveau cycle d'activités </div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-form class="full-width q-pa-sm">
            <div class="row q-mb-sm">
                <q-input type="number" dense outlined v-model="num" ref="num" label="Numéro du cycle" :rules="[ myRule ]" class="col" placeholder="ex. 1">
                </q-input>
            </div>
            <div class="row q-mb-sm">
                <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="start" label="Date de début du cyle" :rules="[ myRule ]" class="col">
                <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy">
                        <q-date v-model="start" mask="YYYY-MM-DD"></q-date>
                    </q-popup-proxy>
                    </q-icon>
                </template>
                </q-input>
            </div>
            <div class="row q-mb-sm">
                <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="end" label="Date de fin du cyle" :rules="[ myRule ]" class="col">
                <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy">
                        <q-date v-model="end" mask="YYYY-MM-DD"></q-date>
                    </q-popup-proxy>
                    </q-icon>
                </template>
                </q-input>
            </div>
            
            <div align="right">
              <q-btn dense
                  @click="saveCycle"
                  label="Enregistrer"
                  color="primary"
                  icon="save"
                  outline
                  type="submit"
                  >
              </q-btn>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'

  export default {
    data () {
      const saveCycle = () => {
      var form = new FormData()
      form.append('name', this.name + this.num)
      form.append('start', this.start)
      form.append('end', this.end)
      form.append('vslaId', this.vslaId)
      if (this.num !== null && this.start !== null && this.end !== null && this.start < this.end) {
          axios.post(this.$urlServer + 'cycleControllers/addCycle.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Le cycle a été enregistré avec succès'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listCycle()
            })
          })
        } else {
          this.$q.dialog({
            title: 'Erreur',
            message: ('Veuillez bien vérifier et compléter tous les champs'),
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
        saveCycle,
        num: null,
        start: null,
        end: null,
        name: 'Cycle ',
        cycle: [],
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8)
      }
    },
    created () {
        this.listCycle()
    },
    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Champs requis')
            }, 800)
            })
        },
        listCycle () {
            axios.get(this.$urlServer + 'cycleControllers/listCycle.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.cycle = dataFront.data
            })
        },
    }
  }
  </script>
  
  <style lang="sass">

  </style>
    