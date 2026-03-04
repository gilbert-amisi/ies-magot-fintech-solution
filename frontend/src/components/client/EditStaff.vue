<template>
    <q-card style="width: 100%;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Modification des informations du staff</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
        <q-card class="q-pa-sm" bordered flat>
            <fieldset>
                <legend class="text-h6 text-bold">Informations sur le staff</legend>
                <q-form class="full-width">
                    <div class="row q-mb-sm">
                        <q-input dense outlined v-model="name" ref="name" label="Nom du staff" :rules="[ myRule ]" class="col"> 
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-select dense outlined v-model="position" ref="position" label="Fonction" class="col"
                        :rules="[ myRule ]" emit-value map-options
                        :options="options">
                        </q-select>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input dense type="email" outlined :rules="[myRule]" v-model="email" ref="email" label="E-mail" class="col">
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input dense type="tel" outlined :rules="[myRule]" v-model="phone" ref="phone" label="Téléphone" mask="#########" class="col" placeholder="e.g. 995656345">  
                        </q-input>
                    </div>
                    <div align="right">
                        <q-btn
                        @click="saveChanges"
                        label="Enregistrer"
                        color="primary"
                        icon="save"
                        outline
                        type="submit">
                        </q-btn>
                    </div>
                </q-form>
            </fieldset>
        </q-card>
        
    </q-card>
  </template>
  <script>
  import axios from 'axios'

  export default {
    props: ['idS', 'nameS', 'positionS', 'emailS', 'phoneS'],
    data () {
      const saveChanges = () => {
      var form = new FormData()
      form.append('staffId', this.idS)
      form.append('name', this.name)
      form.append('position', this.position)
      form.append('email', this.email)
      form.append('phone', this.phone)

      if (this.name !== null && this.position !== null && this.email !== null && this.phone !== null) {
          axios.post(this.$urlServer + 'staffControllers/editStaff.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Modifications enregistrées avec succès'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
                window.location.reload()
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
      return {
        saveChanges,
        name: this.nameS,
        position: this.positionS,
        email: this.emailS,
        phone: this.phoneS,
        options:[
                    { value: 'Program Manager', label: 'Manager Programme' },
                    { label: 'Manager Projet', value: 'Project Manager' },
                    { label: 'Superviseur des AVEC', value: 'VSLA Manager' }
                ],
      }
    },

    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Required')
            }, 800)
            })
        },
        listMembers () {
            axios.get(this.$urlServer + 'memberControllers/listMember.php', {
                params: {
                    vslaId: this.idVSLA
                }
            }).then((dataFront) => {
                this.members = dataFront.data
            })
        },
    }
  }
  </script>
  