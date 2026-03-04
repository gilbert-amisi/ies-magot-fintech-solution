<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar class="bg-grey-1">
        <q-btn color="blue-10" dense v-if="mysession==true"
          flat size="18px"
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <q-toolbar-title class="q-pa-xs">
          <img alt="logo" width="120px" src="~assets/Magonbg.png"/>
        </q-toolbar-title>

        <div class="q-pa-md">
          <q-btn-dropdown v-if="mysession==true"
            flat stretch
            color="blue-10"
            icon-right="account_circle"
          >
            <div class="row no-wrap q-pa-md">
              <div class="column">
                <div class="text-h6 q-mb-md">Paramètres</div>
                <q-toggle v-model="settoggle" :label="typeUser" />
                <q-toggle v-model="settoggle" :label="userphone" />
                <q-btn
                  color="negative"
                  label="Se déconnecter"
                  icon="settings_power"
                  push
                  size="sm"
                  v-close-popup
                  @click="logoutUser()"
                />
              </div>

              <q-separator vertical inset class="q-mx-lg" />

              <div class="column items-center">
                <q-avatar size="85px">
                  <img src="https://cdn.quasar.dev/img/boy-avatar.png">
                </q-avatar>

                <div class="text-subtitle2 text-bold q-mt-md q-mb-xs">{{ username }}</div>
                  <q-btn
                    color="light-blue-10"
                    label="Editer mon profil"
                    icon="edit_note"
                    push @click="showEditUser = true"
                    size="sm"
                    v-close-popup
                  />
              </div>
            </div>
          </q-btn-dropdown>
          <q-dialog v-model="showEditUser" persistent>
            <edit-user></edit-user>
          </q-dialog>
        </div>

      </q-toolbar>
    </q-header>

    <q-drawer v-if="mysession==true"
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-1"
    >
    <q-list separator class="q-px-sm text-subtitle2">
      <q-item v-if="mysession==true && NGOName=='Enabel'">
        <img class="col"
          alt="Client logo"
          src="~assets/enabel.svg"
          
        >
      </q-item>
      <q-item v-if="mysession==true && NGOName=='Care International'">
        <img class="col"
          alt="Client logo"
          src="~assets/Carelogo.png"
        >
      </q-item>
      <q-separator />
      <q-item to="/client" v-if="mysession==true && typeUser=='IES Admin'" exact clickable>
        <q-item-section avatar>
          <q-icon name="handshake"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>NGOs Management</q-item-label>
        </q-item-section>
      </q-item>
      <q-item to="/staff" v-if="mysession==true && (typeUser=='Program Manager' || typeUser=='Project Manager')" exact clickable>
        <q-item-section avatar>
          <q-icon name="badge"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Gestion des staffs</q-item-label>
        </q-item-section>
      </q-item>
      <q-item to="/project" v-if="mysession==true && (typeUser=='Program Manager' || typeUser=='Project Manager')" exact clickable>
        <q-item-section avatar>
          <q-icon name="folder_zip"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Gestion des projets</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/portfolio" v-if="mysession==true && (typeUser=='Program Manager' || typeUser=='Project Manager')" exact clickable>
        <q-item-section avatar>
          <q-icon name="leaderboard"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Portefeuille général</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/vsla" v-if="mysession==true && (typeUser=='Program Manager' || typeUser=='Project Manager' || typeUser=='VSLA Manager')" exact clickable>
        <q-item-section avatar>
          <q-icon name="spa"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Gestion des AVEC</q-item-label>
        </q-item-section>
      </q-item>

      
      <q-item to="/vslahome" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="home"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Page d'accueil</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/member" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="groups"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Registre des membres</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/account" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="balance"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Solde des comptes</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/saving" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="move_to_inbox"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Epargnes déposées</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/social" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="diversity_2"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Fonds social</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/request" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="request_quote"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Demandes de crédits</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/loan" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="outbox"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Crédits accordés</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/donation" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="handshake"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Dons & legs reçus</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/spending" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="how_to_vote"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Dépenses & assistances</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/meeting" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="diversity_3"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Rapports des réunions</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/penalty" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="flutter_dash"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Penalités & Amendes</q-item-label>
        </q-item-section>
      </q-item>


      <q-item to="/share" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="offline_share"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Partages fin cycles</q-item-label>
        </q-item-section>
      </q-item>

      <q-item to="/report" v-if="mysession==true && typeUser!='IES Admin' && typeUser!='Program Manager' && typeUser!='Project Manager' && typeUser!='VSLA Manager'" exact clickable>
        <q-item-section avatar>
          <q-icon name="picture_as_pdf"></q-icon>
        </q-item-section>
        <q-item-section>
          <q-item-label>Rapport financier</q-item-label>
        </q-item-section>
      </q-item>

    </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
// import axios from 'axios'
import vue from 'vue'
import mysession from 'vue-session'
vue.use(mysession)

export default {
  name: 'MainLayout',
  // components: {
  //   EssentialLink
  // },
  data () {
    return {
      mysession: false,
      leftDrawerOpen: false,
      settoggle: false,
      showEditUser: false,
      userId: this.$session.get(1),
      username: this.$session.get(2),
      typeUser: this.$session.get(3),
      emailUser: this.$session.get(4),
      userphone: this.$session.get(5),
      vslaId: this.$session.get(6),
      vslaName: this.$session.get(7),
      memberId: this.$session.get(8),
      vslaLocation: this.$session.get(9),
      vslaCurrency: this.$session.get(10),
      NGOId: this.$session.get(11),
      StaffId: this.$session.get(12),
      NGOName: this.$session.get(13)
    }
  },
  created () {
    if (this.$session.exists()) {
      if (this.$session.get(3) === 'Program Manager') {
        this.$router.push('/staff')
        this.mysession = true
      } else if (this.$session.get(3) === 'Project Manager') {
        this.$router.push('/project')
        this.mysession = true
      } else if (this.$session.get(3) === 'VSLA Manager') {
        this.$router.push('/vsla')
        this.mysession = true
      } else if (this.$session.get(3) === 'IES Admin') {
        this.$router.push('/client')
        this.mysession = true
      } else if (this.$session.get(3) === 'Chairman' || this.$session.get(3) === 'Secretary' || this.$session.get(3) === 'Cashier' || this.$session.get(3) === 'Counter' || this.$session.get(3) === 'Ordinary member') {
        this.$router.push('/vslahome')
        this.mysession = true
      } else {
        this.$q.dialog({
          title: 'Erreur !',
          message: 'Paramètres de connexion incorrect',
          ok: {
            push: true
          },
          cancel: {
            color: 'negative'
          },
          persistent: true
        }).onOk(() => {
          this.$router.push('/')
        })
      }
    } else {
      this.mysession = false
      this.$router.push('/')
    }
  },
  methods: {
    logoutUser () {
      this.$session.destroy()
      window.location.reload()
    }
  },
  components: {
    'edit-user': require('components/user/EditUser.vue').default
  }

}
</script>
