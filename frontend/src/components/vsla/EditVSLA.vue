<template>
    <q-card style="width: 100%;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">{{ nameV }} UPDATING</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
          <div class="row q-mb-sm">
            <q-select dense :options="projects" outlined :rules="[myRule]" v-model="projectId" 
              label="Select Project" class="col" emit-value ref="projectId" map-options
            >
            </q-select>
          </div>
          
          <div class="row q-mb-sm">
              <q-input dense outlined v-model="name" ref="name" label="Name of group" :rules="[ myRule ]" class="col"></q-input>
          </div>
          <div class="row q-mb-sm">
            <q-select dense outlined :options="types" :rules="[myRule]"  v-model="type" ref="type" label="Type of group" class="col" emit-value    
            >
            </q-select>
          </div>
          <div class="row q-mb-sm">
            <q-input dense outlined aria-required="required" ref="start" mask="####-##-##" v-model="start" label="Creation date" :rules="[ myRule ]" class="col">
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
            <q-input dense outlined v-model="province" ref="province" label="Province - Location" :rules="[ myRule ]" class="col"></q-input>
          </div>
          
          <div class="row q-mb-sm">
            <q-input dense outlined v-model="location" ref="location" label="Territory/Village" :rules="[ myRule ]" class="col"></q-input>
          </div>

          <div class="row q-mb-sm">
            <q-select dense outlined class="col"
            :options="[
              'CDF', 'USD', 'EURO'
            ]" 
            :rules="[myRule]"
            v-model="devise" ref="devise" label="Currency to use"  
              emit-value map-options
            >
            </q-select>
          </div>
          <div class="row q-mb-sm">
            <q-input :prefix="devise" type="number" dense outlined v-model="nominalvalue" :rules="[myRule]" ref="nominalvalue" label="Nominal value of a share" class="col"></q-input>
          </div>

          <div class="row q-mb-sm">
            <q-input prefix="%" type="number" dense outlined v-model="creditrate" :rules="[myRule]" ref="creditrate" label="Taux d'emprunt maximal (Plafond)" class="col"></q-input>
          </div>

          <div class="row q-mb-sm">
            <q-input :prefix="devise" type="number" dense outlined v-model="seuil" :rules="[myRule]" ref="seuil" label="Seuil de liquidité de l'AVEC" class="col"></q-input>
          </div>

          <div class="row q-mb-sm">
            <q-select dense :options="staffs" outlined :rules="[myRule]" v-model="staffId" 
              label="Staff Supervisor" class="col" emit-value ref="staffId" map-options
            >
            </q-select>
          </div>
            
          <div align="right">
            <q-btn
                @click="editVSLA"
                label="Save Changes"
                color="primary"
                icon="save"
                outline
                type="submit">
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
    props: ['idVSLA', 'projectV', 'nameV', 'typeV', 'countryV', 'provinceV', 'locationV', 'startV', 'deviseV', 'nominalV', 'creditrateV', 'seuilV',  'staffV'],
    data () {
      const editVSLA = () => {
      var form = new FormData()
      form.append('vslaId', this.idVSLA)
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
      if (this.projectId !== null && this.name !== null && this.type !== null && this.country !== null && this.province !== null && this.location !== null && this.start !== null && this.devise !== null && this.staffId !== null) {
          axios.post(this.$urlServer + 'vslaControllers/editVSLA.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: 'Modifications enregistrés avec succès',
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
        editVSLA,
        projectId: this.projectV,
        staffId: this.staffV,
        name: this.nameV,
        type: this.typeV,
        devise: this.deviseV,
        country: this.countryV,
        province: this.provinceV,
        location: this.locationV,
        start: this.startV,
        nominalvalue: this.nominalV,
        creditrate: this.creditrateV,
        seuil: this.seuilV,
        countries: ['RD Congo', 'RCA', 'Burundi', 'Tanzanie', 'Senegal', 'Uganda', 'Rwanda'],
        provinces: [],
        types: [
            {
              label: 'Village Savings and Loan Association (VSLA)',
              value: 'VSLA'
            },
            {
              label: 'Solidarity Mutual (SOMU)',
              value: 'SOMU'
            },
            {
              label: 'Other Group',
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
      this.listProvinces()
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
        axios.get(this.$urlServer + 'placeControllers/listProvince.php', {
        }).then((dataFront) => {
            this.provinces = dataFront.data
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
    }
  }
  </script>
  