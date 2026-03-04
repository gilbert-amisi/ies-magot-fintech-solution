<template>
  <q-page class="flex flex-center q-pa-sm">
    <q-card bordered style="max-width: 400px; width: 100%;">
        <q-card-section class="flex flex-center">
          <img alt="logo" width="120px" src="~assets/Mago.png"/>
        </q-card-section>
            <q-card-section horizontal class="flex-center q-pa-md">
              <q-form class="full-width">
                <div class="row q-mb-sm">
                  <q-input dense :rules="[ myRule ]" outlined
                    v-model="username" ref="username" label="Téléphone ou E-mail" class="col" 
                  >
                  <template v-slot:prepend>
                      <q-icon name="phone_iphone" class="cursor-pointer"></q-icon>
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
                <div align="center" class="row q-mb-sm">
                  <q-btn size="14px" @click="loginUser()"
                    label="Connexion" color="light-blue-9" type="submit" rounded class="col">
                  </q-btn>
                </div>
              </q-form>
            </q-card-section>
            <q-separator></q-separator>
            <q-card-actions vertical align="center">
              <div class="text-overline" >
                <q-btn dense flat tag="a" target="_blank" href="https://iescongo.com" exact>© IES Congo</q-btn> <q-btn dense flat tag="a" target="_blank" href="https://dmlcongo.com" exact>& DML Congo</q-btn>
              </div>
            </q-card-actions>

          </q-card> 
  </q-page>
</template>
<script>
import axios from 'axios'
import vue from 'vue'
import mysession from 'vue-session'
vue.use(mysession)
export default {
  name: 'PageIndex',
  data () {
    return {
      username: null,
      password: null,
      isPwd: true
    }
  },
  methods: {
    myRule (val) {
      return new Promise((resolve, reject) => {
        setTimeout(() => {
          resolve(!!val || '* Champs requis')
        }, 1000)
      })
    },
    loginUser () {
      axios.get(this.$urlServer + 'loginControllers/login.php', {
        params: {
          username: this.username,
          password: this.password
        }
      }).then((res) => {
        this.$session.set(1, res.data[0].UserId)
        this.$session.set(2, res.data[0].Username)
        this.$session.set(3, res.data[0].Usertype)
        this.$session.set(4, res.data[0].Useremail)
        this.$session.set(5, res.data[0].Userphone)
        this.$session.set(6, res.data[0].vslaId)
        this.$session.set(7, res.data[0].vslaName)
        this.$session.set(8, res.data[0].memberId)
        this.$session.set(9, res.data[0].Location)
        this.$session.set(10, res.data[0].Devise)
        this.$session.set(11, res.data[0].NGOId)
        this.$session.set(12, res.data[0].StaffId)
        this.$session.set(13, res.data[0].NGOName)
        window.location.reload()
      })
    }
  }
}
</script>
