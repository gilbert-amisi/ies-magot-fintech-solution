<template>
    <q-card style="width: 100%; max-width: 900px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">ENREGISTREMENT D'UNE AVEC</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width row mt-4 q-gutter-xs">
          <fieldset class="col-md q-pa-sm">
              <legend class="text-subtitle2">Denomination et localisation de l'AVEC</legend>
              <div class="row q-mb-sm">
                <q-select dense :options="projects" outlined :rules="[myRule]" v-model="projectId" 
                  label="Sélectionner le projet" class="col" emit-value ref="projectId" map-options
                >
                </q-select>
              </div>
          
              <div class="row q-mb-sm">
                  <q-input dense outlined v-model="name" ref="name" label="Nom du groupe" :rules="[ myRule ]" class="col"></q-input>
              </div>
              
              <div class="row q-mb-sm">
                <q-input dense outlined aria-required="required" ref="start" mask="####-##-##" v-model="start" label="Date de création" :rules="[ myRule ]" class="col">
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
                <q-select dense outlined :options="countries" :rules="[myRule]" 
                  v-model="country" ref="country" label="Pays de localisation"
                  @input="listProvinces"
                  use-input use-chips
                  input-debounce="0"
                  new-value-mode="add"
                  class="col" emit-value   
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-select dense :options="provinces" outlined :rules="[myRule]" v-model="provinceId" 
                    label="Province" class="col" 
                    emit-value ref="provinceId"
                    @blur="listAreas(provinceId)" map-options
                    use-input use-chips
                    input-debounce="0"
                    new-value-mode="add"
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-select dense :options="territories" outlined :rules="[myRule]" 
                    label="Territoire/District" class="col" v-model="territoryId" emit-value 
                    ref="territoryId" map-options
                    use-input use-chips
                    input-debounce="0"
                    new-value-mode="add"
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-input dense outlined v-model="village" ref="village" label="Village/Cité" :rules="[ myRule ]" class="col"></q-input>
              </div>
          
          </fieldset>

          <fieldset class="col-md q-pa-sm">
              <legend class="text-subtitle2">Politiques internes et Règles de gestion de l'AVEC</legend>
              <div class="row q-mb-sm">
                <q-select dense outlined :options="types" :rules="[myRule]"  v-model="type" ref="type" label="Type du groupe" class="col" emit-value    
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-select dense outlined class="col"
                :options="[
                  'CDF', 'USD', 'EURO'
                ]" 
                :rules="[myRule]"
                v-model="devise" ref="devise" label="Dévise monétaire à utiliser"  
                  emit-value map-options
                  use-input use-chips
                  input-debounce="0"
                  new-value-mode="add"
                >
                </q-select>
              </div>
              <div class="row q-mb-sm">
                <q-input :prefix="devise" type="number" dense outlined v-model="nominalvalue" :rules="[myRule]" ref="nominalvalue" label="Valeur nominale d'une part" class="col"></q-input>
              </div>

              <div class="row q-mb-sm">
                <q-input prefix="%" type="number" dense outlined v-model="creditrate" :rules="[myRule]" ref="creditrate" label="Taux d'emprunt maximal (Plafond)" class="col"></q-input>
              </div>

              <div class="row q-mb-sm">
                <q-input :prefix="devise" type="number" dense outlined v-model="seuil" :rules="[myRule]" ref="seuil" label="Seuil de liquidité de l'AVEC" class="col"></q-input>
              </div>

              <div class="row q-mb-sm">
                <q-select dense :options="staffs" outlined :rules="[myRule]" v-model="staffId" 
                  label="Superviseur (Staff de l'ONG)" class="col" emit-value ref="staffId" map-options
                >
                </q-select>
              </div>
            
              <div align="right">
                <q-btn
                    @click="addVSLA"
                    label="Enregistrer"
                    color="light-blue-10"
                    icon="save"
                    push rounded
                    type="submit">
                </q-btn>
              </div>
        </fieldset>
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
    data () {
      const addVSLA = () => {
      var form = new FormData()
      form.append('projectId', this.projectId)
      form.append('name', this.name)
      form.append('type', this.type)
      form.append('country', this.country)
      if (this.country=='RD Congo') {
        form.append('province', this.provinces.find(option => option.value === this.provinceId).label)
        form.append('location', this.territories.find(option => option.value === this.territoryId).label +' / ' + this.village)
      } else {
        form.append('province', this.provinceId)
        form.append('location', this.territoryId +' / ' + this.village)
      }
      
      form.append('start', this.start)
      form.append('devise', this.devise)
      form.append('nominalvalue', this.nominalvalue)
      form.append('creditRate', this.creditrate)
      form.append('seuil', this.seuil)
      form.append('staffId', this.staffId)
      form.append('creditrate', this.creditrate)
      if (this.projectId !== null && this.name !== null && this.type !== null && this.country !== null && this.provinceId !== null && this.village !== null && this.start !== null && this.devise !== null && this.staffId !== null) {
          axios.post(this.$urlServer + 'vslaControllers/addVSLA.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: 'Group successfully registered',
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.$router.push('/vsla')
            })
          })
        } else {
          this.$q.dialog({
            website: 'Error',
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
        addVSLA,
        projectId: null,
        staffId: null,
        name: null,
        type: null,
        devise: null,
        creditrate: null,
        seuil: null,
        country: null,
        provinceId: null,
        territoryId: null,
        village: null,
        start: null,
        nominalvalue: null,
        countries: ['RD Congo', 'RCA', 'Burundi', 'Tanzanie', 'Senegal', 'Uganda', 'Rwanda'],
        provinces: [],
        territories: [],
        types: [
            {
              label: 'Village Savings and Loan Association (AVEC)',
              value: 'VSLA'
            },
            {
              label: 'Solidarity Mutual (MUSO)',
              value: 'SOMU'
            },
            {
              label: 'Other Group (Autre)',
              value: 'Other'
            }
        ],
        projects: [],
        staffs: [],
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        NGOId: this.$session.get(11),
        StaffId: this.$session.get(12),
        NGOName: this.$session.get(13)
      }
    },
    created () {
      this.listProject()
      this.listStaff()
    },
    methods: {
      myRule (val) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            resolve(!!val || '* Required')
          }, 1000)
        })
      },
      listProject () {
        axios.get(this.$urlServer + 'projectControllers/listProject.php', {
          params: {
            ngoId: this.NGOId
          }
        }).then((dataFront) => {
          this.projects = dataFront.data
        })
      },
      listProvinces () {
        if (this.country=='RD Congo') {
          axios.get(this.$urlServer + 'placeControllers/listProvince.php', {
          }).then((dataFront) => {
              this.provinces = dataFront.data
          })
      }
        
      },
      listAreas (provinceId) {
        axios.get(this.$urlServer + 'placeControllers/listTerritory.php', {
            params: {
              provinceId: provinceId
            }
        }).then((dataFront) => {
            this.territories = dataFront.data
        })
      },
      listStaff () {
        axios.get(this.$urlServer + 'staffControllers/listVSLAManager.php', {
          params: {
            ngoId: this.NGOId
          }
        }).then((dataFront) => {
          this.staffs = dataFront.data
        })
      },
      createValue (val, done) {
        if (val.length > 2) {
          if (!countries.includes(val)) {
            done(val, 'add-unique')
          }
        }
      },
    }
  }
  </script>
  