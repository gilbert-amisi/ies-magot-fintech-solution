<template>
    <q-card style="width: 100%; max-width: 500px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Edition du profil utilisateur</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
        <q-card class="q-pa-sm" bordered flat>
            <fieldset>
                <legend class="text-h6 text-bold">Informations sur votre profil</legend>
                <q-form class="full-width q-pa-sm">
                    <div class="row q-mb-sm">
                        <q-input dense :rules="[ myRule ]" outlined  type="telk" mask="#########"
                            v-model="username" ref="username" label="Nom d'utilisateur" class="col" 
                        >
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input dense :rules="[ myRule ]" outlined  type="telk" mask="#########"
                            v-model="phone" ref="phone" label="Numéro de téléphone" class="col" 
                        >
                            <template v-slot:prepend>
                                <q-icon name="phone_iphone" class="cursor-pointer"></q-icon>
                            </template>
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input dense outlined  type="telk" mask="#########"
                            v-model="email" ref="email" label="E-mail" class="col" 
                        >
                            <template v-slot:prepend>
                                <q-icon name="email" class="cursor-pointer"></q-icon>
                            </template>
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input dense v-model="password" label="Mot de passe" ref="password" class="col" :rules="[ myRule ]"
                            :type="isPwd ? 'password' : 'text'" outlined>
                            <template v-slot:prepend>
                                <q-icon name="lock" class="cursor-pointer"></q-icon>
                            </template>
                            <template v-slot:append>
                                <q-icon :name="isPwd ? 'visibility_off' : 'visibility'"
                                    class="cursor-pointer" @click="isPwd = !isPwd"
                                />
                            </template>
                        </q-input>
                    </div>
                    <div class="row q-mb-sm" align="center">
                        <q-btn class="col" rounded
                            @click="saveChanges"
                            label="Enregistrer les modifications"
                            color="light-blue-10"
                            icon="save"
                            
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
    data () {
      const saveChanges = () => {
      var form = new FormData()
      form.append('userId', this.userId)
      form.append('username', this.username)
      form.append('email', this.email)
      form.append('phone', this.phone)
      form.append('password', this.password)
      if (this.username !== null && this.phone !== null && this.password) {
          axios.post(this.$urlServer + 'loginControllers/selfEditUser.php', form, {
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
            })
          })
        } else {
          this.$q.dialog({
            title: 'Error',
            message: ('Veuillez renseigner tous les champs obligatoire!'),
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
        userId: this.$session.get(1),
        username: this.$session.get(2),
        email: this.$session.get(4),
        phone: this.$session.get(5),
        password: null,
        isPwd: true
      }
    },

    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Required')
            }, 800)
            })
        }
    }
  }
  </script>
  