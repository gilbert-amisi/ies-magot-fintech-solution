<template>
    <q-card style="width: 100%; max-width: 900px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Modification des informations du membre</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
        <q-card class="q-pa-sm" bordered flat>
          <fieldset>
            <legend class="text-h6 text-bold">Informations sur le membre</legend>
            <q-form class="full-width row mt-4 q-pa-sm q-gutter-sm">
              <div class="col-md">
                <div class="row q-mb-sm">
                  <q-input dense outlined v-model="name" ref="name" label="Nom" :rules="[ myRule ]" class="col">
                  </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-select dense outlined :options="genders" :rules="[myRule]"  v-model="gender" ref="gender" label="Genre" class="col" emit-value    
                    >
                    </q-select>
                </div>
                <div class="row q-mb-sm">
                    <q-select dense outlined :options="ages" :rules="[myRule]"  v-model="age" ref="age" label="Tranche d’âge" class="col" emit-value    
                    >
                    </q-select>
                </div>
                <div class="row q-mb-sm">
                  <q-select dense outlined :options="educations" :rules="[myRule]"  v-model="education" ref="education" label="Niveau d'étude" class="col" emit-value    
                  >
                  </q-select>
                </div>
                <div class="row q-mb-sm">
                <q-select dense outlined :options="activities" :rules="[myRule]"  v-model="activity" ref="activity" label="Main activity" class="col" emit-value    
                >
                </q-select>
              </div>
                
              </div>

            <div class="col-md">
               <div class="row q-mb-sm">
                <q-select dense outlined :options="handicaps" :rules="[myRule]"  v-model="marginalization" ref="marginalization" label="Appartient à un groupe marginalisé ? (Handicap, Albinos, Pygmée,...)" class="col" emit-value map-options   
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-select dense outlined :options="positions" :rules="[myRule]" v-model="position" ref="position" label="Position dans le groupe" class="col" map-options emit-value    
                >
                </q-select>
              </div> 
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="operators" :rules="[myRule]" v-model="operator" ref="operator" label="Opérateur téléphonique utilisé pour le transfert" class="col" 
                  use-input use-chips input-debounce="0" new-value-mode="add" emit-value    
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-input dense type="tel" outlined :rules="[myRule]" v-model="phone" ref="phone" label="Numéro de téléphone pour le transfert d'argent" mask="#########" class="col" placeholder="e.g. 995656345">
                </q-input>
              </div>
                
            <div class="row q-mb-sm" align="center">
                <q-btn class="col" rounded
                    @click="editMember"
                    label="Enregistrer les modifications"
                    color="light-blue-10"
                    icon="save"
                    
                    type="submit">
                </q-btn>
            </div>
            </div> 
          </q-form>
          </fieldset>
          
        </q-card>
        
    </q-card>
  </template>
  <script>
  import axios from 'axios'

  export default {
    props: ['idM', 'nameM', 'ageM', 'genderM', 'educationM', 'activityM', 'positionM', 'marginalizationM', 'operatorM', 'phoneM'],
    data () {
      const editMember = () => {
      var form = new FormData()
      form.append('memberId', this.idM)
      form.append('name', this.name)
      form.append('gender', this.gender)
      form.append('age', this.age)
      form.append('education', this.education)
      form.append('activity', this.activity)
      form.append('position', this.position)
      form.append('marginalization', this.marginalization)
      form.append('operator', this.operator)
      form.append('phone', this.phone)
      form.append('vslaId', this.idVSLA)
      if (this.name !== null && this.gender !== null && this.age !== null && this.education !== null && this.activity !== null && this.position !== null && this.operator !== null && this.phone !== null) {
          axios.post(this.$urlServer + 'memberControllers/editMember.php', form, {
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
        editMember,
        name: this.nameM,
        gender: this.genderM,
        age: this.ageM,
        education: this.educationM,
        activity: this.activityM,
        position: this.positionM,
        marginalization: this.marginalizationM,
        operator: this.operatorM,
        phone: this.phoneM,
        genders: [
            {label: 'Masculin', value: 'M'},
            {label: 'Féminin', value: 'F'}
        ],
        ages: ['Under 18', '18 to 24', '25 to 34', '35 to 44', '45 to 64', '65 or over'],
        educations: ['Never studied', 'Literate', 'Primary', 'Secondary', 'University'],
        activities: ['Agriculture/Livestock', 'Commerce', 'Humanitarian', 'State Agent', 'Mining', 'Religious/Pastoral', 'Other'],
        positions: [
          {label: 'Président', value: 'Chairman'},
          {label: 'Secrétaire', value: 'Secretary'},
          {label: 'Trésorier/Caissier', value: 'Cashier'},
          {label: 'Compteur', value: 'Counter'},
          {label: 'Membre ordinaire', value: 'Ordinary member'}
        ],
        handicaps: [
          {label: 'Oui', value: 'Marginalized'},
          {label: 'Non', value: 'Not Marginalized'}
        ],
        operators: ['Airtel Money', 'M-Pesa', 'Orange Money', 'Afri Money']
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
  