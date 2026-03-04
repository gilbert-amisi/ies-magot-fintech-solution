<template>
  <q-page class="q-pa-xs">
    <q-card>
      <q-card-section class="bg-grey-1 text-white">
        <div class="text-h6 text-light-blue-10 text-center">LES MEMBRES DU GROUPE</div>
      </q-card-section>
      <q-separator></q-separator>
      <!-- <q-tabs v-model="tab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator shrink>
        <q-tab no-caps name="list" label="Liste des membres" icon="sort_by_alpha" class="text-primary"/>
        <q-tab no-caps v-if="typeUser == 'Chairman' || typeUser == 'Secretary'" name="add" label="Ajouter un membre" icon="group_add" class="text-accent" />
      </q-tabs>
      <q-separator /> -->
      
      <!-- <q-tab-panels v-model="tab">
        <q-tab-panel name="list"> -->
          <q-card flat>
            <q-table bordered flat separator="cell" class="col q-pa-sm"
              :data="members"
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
                    <q-card-section class="text-subtitle2 bg-grey-1">
                      <q-checkbox dense v-model="props.selected" :label="props.row.label" />
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
                    <!-- <q-separator></q-separator>
                    <q-card-actions align="around" v-if="typeUser == 'Chairman' || typeUser == 'Secretary'">
                      <q-btn side flat no-caps dense icon="edit" color="primary" label="Modifier"></q-btn>
                      <q-btn side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn>
                    </q-card-actions> -->
                  </q-card>
                </div>
              </template>

              <template v-slot:top-right>
                  <q-input dense outlined debounce="500" v-model="filter" placeholder="Rechercher un membre" class="col">
                  <template v-slot:append>
                      <q-icon name="search" />
                  </template>
                  </q-input>
              </template>
          </q-table>
        </q-card>
        <!-- </q-tab-panel> -->
        <!-- <q-tab-panel v-if="typeUser == 'Chairman' || typeUser == 'Secretary'" name="add"> 
          <q-card flat>
            <q-card-section class="bg-accent">
              <q-item-label class="text-subtitle2 text-bold text-white">Nouveau membre du groupe</q-item-label>
            </q-card-section>
            <q-separator></q-separator>
            <q-form class="full-width q-pa-sm">
              <div class="row q-mb-sm">
                  <q-input dense outlined v-model="name" ref="name" label="Nom complet du membre" :rules="[ myRule ]" class="col">
                  </q-input>
              </div>
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="genders" :rules="[myRule]"  v-model="gender" ref="gender" label="Genre ou sexe" class="col" emit-value map-options   
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="ages" :rules="[myRule]"  v-model="age" ref="age" label="Tranche d'âge" class="col" emit-value map-options   
                  >
                  </q-select>
              </div>

              <div class="row q-mb-sm">
                  <q-select dense outlined :options="positions" :rules="[myRule]"  v-model="position" ref="position" label="Poste dans le groupe" class="col" emit-value map-options  
                  >
                  </q-select>
              </div> 
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="educations" :rules="[myRule]"  v-model="education" ref="education" label="Niveau d'étude" class="col" emit-value map-options   
                  >
                  </q-select>
              </div>
              
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="activities" :rules="[myRule]"  v-model="activity" ref="activity" label="Activité principale" class="col" emit-value map-options 
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="handicaps" :rules="[myRule]"  v-model="marginalization" ref="marginalization" label="Membre marginalisé? (Handicap, Albinos, Pygmée,...)" class="col" emit-value map-options   
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="operators" :rules="[myRule]"  v-model="operator" ref="operator" label="Opérateur mobile ou Réseau" class="col" emit-value    
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-input dense type="tel" outlined :rules="[myRule]" v-model="phone" ref="phone" label="Numéro de téléphone" mask="#########" class="col" placeholder="ex. 995656345">
                </q-input>
              </div>
                
              <div align="right">
                <q-btn dense
                    @click="addMember"
                    label="Enregistrer"
                    color="accent"
                    icon="save"
                    outline
                    type="submit">
                </q-btn>
              </div>
            </q-form>
          </q-card>
        </q-tab-panel> -->
      <!-- </q-tab-panels> -->
    </q-card>
  </q-page>
</template>

<script>
import axios from 'axios'

const columns = [
      { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
      {
        name: 'label',
        required: true,
        label: 'Nom complet',
        align: 'left',
        field: row => row.label,
        format: val => `${val}`,
        sortable: true
      },
      { name: 'Gender', align: 'center', label: 'Genre', field: 'Gender', sortable: true },
      { name: 'Age',  align: 'left', label: "Tranche d'âge", field: 'Age', sortable: true },
      { name: 'Education', align: 'left', label: "Niveau d'étude", field: 'Education', sortable: true  },
      { name: 'Activity', align: 'left', label: 'Activité principale', field: 'Activity', sortable: true  },
      { name: 'Position', align: 'left', label: 'Poste occupé', field: 'Position', sortable: true  },
      { name: 'Phone', align: 'right', label: 'Numéro de téléphone', field: 'Phone' },
      { name: 'Operator', align: 'left', label: 'Opérateur Mobile', field: 'Operator' }
      
  ]
export default {
  data () {
    const addMember = () => {
    var form = new FormData()
    form.append('name', this.name)
    form.append('gender', this.gender)
    form.append('age', this.age)
    form.append('education', this.education)
    form.append('activity', this.activity)
    form.append('position', this.position)
    form.append('marginalization', this.marginalization)
    form.append('operator', this.operator)
    form.append('phone', this.phone)
    form.append('vslaId', this.vslaId)
    if (this.name !== null && this.gender !== null && this.age !== null && this.education !== null && this.activity !== null && this.position !== null && this.operator !== null && this.phone !== null) {
        axios.post(this.$urlServer + 'memberControllers/addMember.php', form, {
        }).then((res) => {
          this.$q.dialog({
            title: 'Success',
            message: ('Le membre du groupe a été enregistré avec succès'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            this.listMembers()
          })
        })
      } else {
        this.$q.dialog({
          title: 'Error',
          message: ('Veuillez compléter tous les champs requis'),
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
      addMember,
      columns,
      tab: 'list',
      name: null,
      gender: null,
      age: null,
      education: null,
      activity: null,
      marginalization: null,
      position: null,
      operator: null,
      phone: null,
      filter: '',
      selected: [],
      genders: [
          {label: 'Homme', value: 'M'},
          {label: 'Femme', value: 'F'}
      ],
      ages: [
        {value: 'Under 18', label : 'Moins de 18 ans'},
        {value: '18 to 24', label : '18 à 24 ans'},
        {value: '25 to 34', label : '25 à 34 ans'},
        {value: '35 to 44', label : '35 à 44 ans'},
        {value: '45 to 64', label : '45 à 64 ans'},
        {value: '65 or over', label : '65 ans ou plus'}
      ],
      educations: [
        {value: 'Never studied', label : 'Jamais étudié'},
        {value: 'Literate', label : 'Alphabétisé(e)'},
        {value: 'Primary', label : 'Primaire'},
        {value: 'Secondary', label : 'Secondaire'},
        {value: 'University', label : 'Universitaire'}
      ],
      activities: [
        {value: 'Agriculture/Livestock', label : 'Agriculture/Elevage'},
        {value: 'Commerce', label : 'Commerce'},
        {value: 'Humanitarian', label : 'Humanitaire'},
        {value: 'State Agent', label : "Agent de l'Etat"},
        {value: 'Mining', label : 'Activité minière'},
        {value: 'Religious/Pastoral', label : 'Religieux/Pasteur'},
        {value: 'Other', label : 'Autre'}
      ],
      handicaps: [
          {label: 'Oui', value: 'Marginalized'},
          {label: 'Non', value: 'Not Marginalized'}
      ],
      positions: [
        {value: 'Chairman', label : 'Président'},
        {value: 'Secretary', label : 'Secrétaire'},
        {value: 'Cashier', label : 'Caissier'},
        {value: 'Counter', label : "Compteur"},
        {value: 'Ordinary member', label : 'Membre ordinaire'}
      ],
      operators: ['Airtel Money', 'M-Pesa', 'Orange Money'],
      members: [],
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
      this.listMembers()
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
  }
}
</script>

<style lang="sass">
.grid-style-transition
  transition: transform .28s, background-color .28s
</style>
  