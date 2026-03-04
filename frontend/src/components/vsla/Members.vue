<template>
    <q-card style="width: 100%; max-width: 1400px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">MEMBRES DE {{ typeVSLA.toUpperCase() }} {{ nameVSLA.toUpperCase() }} </q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
        <q-card class="q-pa-sm" bordered flat>
          <fieldset>
            <legend class="text-h6 text-bold">Ajouter un membre du groupe</legend>
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
                
              </div>
              <div class="col-md">
                
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
              <div class="row q-mb-sm">
                <q-select dense outlined :options="handicaps" :rules="[myRule]"  v-model="marginalization" ref="marginalization" label="Appartient à un groupe marginalisé ? (Handicap, Albinos, Pygmée,...)" class="col" emit-value map-options   
                >
                </q-select>
              </div>
              
            </div>

            <div class="col-md">
              <div class="row q-mb-sm">
                <q-select dense outlined :options="positions"  v-model="position" ref="position" label="Position dans le groupe" class="col" map-options emit-value    
                >
                </q-select>
              </div> 
              <div class="row q-mb-sm">
                  <q-select dense outlined :options="operators"  v-model="operator" ref="operator" label="Opérateur téléphonique utilisé pour le transfert" class="col" 
                  use-input use-chips input-debounce="0" new-value-mode="add" emit-value    
                  >
                  </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-input dense type="tel" outlined v-model="phone" ref="phone" label="Numéro de téléphone pour le transfert d'argent" mask="#########" class="col" placeholder="e.g. 995656345">
                </q-input>
              </div>
                
            <div class="row q-mb-sm" align="center">
                <q-btn class="col" rounded dense
                    @click="addMember"
                    label="Enregistrer"
                    color="light-blue-10"
                    icon="save"
                    push
                    type="submit">
                </q-btn>
            </div>
            </div> 
          </q-form>
          </fieldset>
          
        </q-card>
        <div class="row mt-4">
          <div class="col-md-12">
            <q-table bordered flat separator="cell"
              :data="members"
              :columns="columns"
              row-key="name"
              binary-state-sort
              :filter="filter" 
              >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="value" :props="props">
                      {{ props.row.value }}
                  </q-td>         
                  <q-td key="label" :props="props">
                      {{ props.row.label }}
                  </q-td>
                  
                  <q-td key="Gender" :props="props">
                      {{ props.row.Gender }}
                  </q-td>
                  <q-td key="Age" :props="props">
                      {{ props.row.Age }}
                  </q-td>
                  <q-td key="Education" :props="props">
                  <div class="text-pre-wrap">{{ props.row.Education }} </div>
                  </q-td>
                  <q-td key="Activity" :props="props">
                      {{ props.row.Activity }}
                  </q-td>
                  <q-td key="Position" :props="props">
                      {{ props.row.Position }}
                  </q-td>
                  <q-td key="Marginalization" :props="props">
                      {{ props.row.Marginalization }}
                  </q-td>
                  <q-td key="Phone" :props="props">
                      {{ props.row.Phone }}
                  </q-td>
                  <q-td key="Operator" :props="props">
                      {{ props.row.Operator }}
                  </q-td>
                  <q-td key="Action" :props="props">
                      <q-btn side flat dense icon="edit" color="primary" title="Edit Member"
                        @click="editMember(props.row.value, props.row.label, props.row.Gender, props.row.Age, props.row.Activity, props.row.Position, props.row.Marginalization, props.row.Position, props.row.Operator , props.row.Phone )">
                      </q-btn>
                      <q-btn side flat dense icon="delete" color="negative" title="Delete Member"></q-btn>
                  </q-td>   
                </q-tr>
              </template>
              <template v-slot:top-right >
                  <q-input filled dense debounce="500" v-model="filter" placeholder="Search Member" class="col">
                  <template v-slot:append>
                      <q-icon name="search" />
                  </template>
                  </q-input>
              </template>
              <template v-slot:top-left>
                  <q-item-label class="text-h6 text-dark text-bold">Repertoire des membres du groupe</q-item-label>
              </template>

            </q-table>
          </div>
          <q-dialog v-model="showEditMember" persistent>
            <edit-member :idM="id" :nameM="name" :ageM="age" :genderM="gender" :educationM="education" :activityM="activity" :positionM="position" :marginalizationM="marginalization" :operatorM="operator" :phoneM="phone"></edit-member>
          </q-dialog>
        </div>
    </q-card>
  </template>
  <script>
  import axios from 'axios'

  const columns = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true, summarizable: true },
        {
          name: 'label',
          required: true,
          label: 'Name of Member',
          align: 'left',
          field: row => row.label,
          format: val => `${val}`,
          sortable: true
        },
        { name: 'Gender', align: 'center', label: 'Gender', field: 'Gender', sortable: true },
        { name: 'Age',  align: 'left', label: 'Age', field: 'Age', sortable: true },
        { name: 'Education', align: 'left', label: 'Education level', field: 'Education', sortable: true  },
        { name: 'Activity', align: 'left', label: 'Main Activity', field: 'Activity', sortable: true  },
        { name: 'Position', align: 'left', label: 'Position in the group', field: 'Position', sortable: true  },
        { name: 'Marginalization', align: 'left', label: 'Marginalization status', field: 'Marginalization', sortable: true  },
        { name: 'Phone', align: 'right', label: 'Phone Number', field: 'Phone' },
        { name: 'Operator', align: 'left', label: 'Mobile Operator', field: 'Operator' },
        { name: 'Action', align: 'center', label: 'Action', field: 'Action' }
        
    ]
  export default {
    props: ['idVSLA', 'nameVSLA', 'typeVSLA'],
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
      form.append('vslaId', this.idVSLA)
      if (this.name !== null && this.gender !== null && this.age !== null && this.education !== null && this.activity !== null && this.position !== null && this.operator !== null && this.phone !== null) {
          axios.post(this.$urlServer + 'memberControllers/addMember.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Member of Group successfully saved'),
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
        addMember,
        columns,
        showEditMember: false,
        id: null,
        name: null,
        gender: null,
        age: null,
        education: null,
        activity: null,
        position: null,
        marginalization: null,
        operator: null,
        phone: null,
        filter: '',
        genders: [
            {label: 'Male', value: 'M'},
            {label: 'Female', value: 'F'}
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
          {label: 'Yes', value: 'Marginalized'},
          {label: 'No', value: 'Not Marginalized'}
        ],
        operators: ['Airtel Money', 'M-Pesa', 'Orange Money', 'Afri Money'],
        members: []
      }
    },
    created () {
        this.listMembers()
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
        editMember(id, name, gender, age, activity, education, marginalization, position, operator,  phone) {
            this.id = id
            this.name = name
            this.gender = gender
            this.age = age
            this.activity = activity
            this.education = education
            this.marginalization = marginalization
            this.position = position
            this.operator = operator
            this.phone = phone
            this.showEditMember= true
        },
    },
    components: {
        'edit-member': require('components/vsla/EditMember.vue').default
    }
  }
  </script>
  