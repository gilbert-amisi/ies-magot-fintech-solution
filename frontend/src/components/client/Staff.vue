<template>
  <q-card style="width: 100%; max-width: 1200px;">
    <q-card-section class="row bg-grey-1">
      <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">{{ nameClient}} - Staffs Management</q-item-label>
      <q-space></q-space>
      <q-btn flat round dense icon="close" v-close-popup></q-btn>
    </q-card-section>
    <q-separator horizontal/>
    <q-card-section>
      <div class="row mt-4">
        <q-card class="col-md-5 q-pa-sm">
          <q-card-section>
            <q-item-label class="text-h6">Adding new staff</q-item-label>
          </q-card-section>
          
          <q-form class="full-width">
            <div class="row q-mb-sm">
              <q-input dense outlined v-model="name" ref="name" label="Name" :rules="[ myRule ]" class="col">
                
              </q-input>
            </div>
            <div class="row q-mb-sm">
              <q-select dense outlined v-model="position" ref="position" label="Position" class="col" 
                :rules="[ myRule ]"
                :options="[
                  'Program Manager',
                  'Project Manager',
                  'VSLA Manager'
                ]">
              </q-select>
            </div>
            <div class="row q-mb-sm">
              <q-input dense type="email" outlined :rules="[myRule]" v-model="email" ref="email" label="E-mail" class="col">
              </q-input>
            </div>
            <div class="row q-mb-sm">
              <q-input dense type="tel" outlined :rules="[myRule]" v-model="phone" ref="phone" label="Phone number" mask="#########" class="col" placeholder="e.g. 995656345">  
              </q-input>
            </div>
            <div align="right">
              <q-btn
                @click="addStaff"
                label="Save the staff"
                color="primary"
                icon="save"
                outline
                type="submit">
              </q-btn>
            </div>
          </q-form>
        </q-card>

        <div class="col-md-7">
          <q-table bordered separator="cell"
            title="List of staffs"
            :data="staffs"
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
                  <q-td key="Position" :props="props">
                  {{ props.row.Position }}
                  </q-td>
                  <q-td key="Email" :props="props">
                  {{ props.row.Email }}
                  </q-td>
                  <q-td key="Phone" :props="props">
                  {{ props.row.Phone }}
                  </q-td>
                  <q-td key="Action" :props="props">
                      <q-btn side no-caps flat dense color="primary" icon="edit" title="Edit staff" ></q-btn>
                      <q-btn side no-caps flat dense color="red" icon="delete" title="Delete  staff"></q-btn>
                  </q-td>   
                </q-tr>
              </template>
              <template v-slot:top-right>
                  <q-input filled dense debounce="500" v-model="filter" placeholder="Search a staff" class="col">
                  <template v-slot:append>
                      <q-icon name="search" />
                  </template>
                  </q-input>
              </template>

          </q-table>

        </div>

      </div>
      
    </q-card-section>

  </q-card>
</template>
<script>
  import axios from 'axios'

  const columns = [
    { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
    { name: 'label', required: true, label: 'Name', align: 'left', field: 'label', sortable: true },
    { name: 'Position', align: 'left', label: 'Position', field: 'Postion', sortable: true },
    { name: 'Email', align: 'left', label: 'E-mail', field: 'Email', sortable: true },
    { name: 'Phone', align: 'right', label: 'Phone number', field: 'Phone', sortable: true },
    { name: 'Action', align: 'center', label: 'Actions', field: 'Action' }
  ]
  export default {
    props: ['idClient', 'nameClient'],
    data () {
      const addStaff = () => {
      var form = new FormData()
      form.append('name', this.name)
      form.append('position', this.position)
      form.append('email', this.email)
      form.append('phone', this.phone)
      form.append('idNGO', this.idClient)
      if (this.name !== null && this.email !== null && this.phone !== null) {
          axios.post(this.$urlServer + 'staffControllers/addStaff.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('NGO Staff successfully saved'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listStaff()
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
        columns,
        addStaff,
        name: null,
        position: null,
        email: null,
        phone: null,
        filter: '',
        staffs: []
      }
    },
    created () {
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
      listStaff () {
        axios.get(this.$urlServer + 'staffControllers/listStaff.php', {
            params: {
              ngoId: this.idClient
            }
        }).then((dataFront) => {
            this.staffs = dataFront.data
        })
      },
    }
  }
  </script>
  