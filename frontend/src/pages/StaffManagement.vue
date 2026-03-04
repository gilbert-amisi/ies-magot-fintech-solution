<template>
    <q-page class="q-pa-xs">
        <q-card>
        <q-card-section class="bg-grey-1">
            <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">GESTION DES STAFFS - {{ NGOName.toUpperCase()}}</q-item-label>  
        </q-card-section>
        <q-separator horizontal/>
        <q-card-section>
            <div class="row mt-4">
            <q-card class="col-md-5 q-pa-sm">
                <q-card-section>
                <q-item-label class="text-h6">Ajouter un staff</q-item-label>
                </q-card-section>
                
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
                    @click="addStaff"
                    label="Enregistrer"
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
                title="Liste des staffs utilisateurs"
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
                            <q-btn side no-caps flat dense color="primary" icon="edit" title="Edit staff"
                               @click="editStaff(props.row.value, props.row.label, props.row.Position, props.row.Email, props.row.Phone )"
                            ></q-btn>
                            <q-btn side no-caps flat dense color="red" icon="delete" title="Delete  staff"></q-btn>
                        </q-td>   
                      </q-tr>
                    </template>
                    <template v-slot:top-right>
                        <q-input filled dense debounce="500" v-model="filter" placeholder="Rechercher un staff" class="col">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                        </q-input>
                    </template>
    
                </q-table>
                <q-dialog v-model="showEditStaff" persistent>
                    <edit-staff :idS="idStaff" :nameS="name" :positionS="position" :emailS="email" :phoneS="phone"></edit-staff>
                </q-dialog>
            </div>
    
            </div>
            
        </q-card-section>
    
        </q-card>
    </q-page>
  </template>
  <script>
    import Vue from 'vue'
    import axios from 'axios'
    import mysession from 'vue-session'
    Vue.use(mysession)
  
    const columns = [
      { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
      { name: 'label', required: true, label: 'Nom du staff', align: 'left', field: 'label', sortable: true },
      { name: 'Position', align: 'left', label: 'Fonction', field: 'Postion', sortable: true },
      { name: 'Email', align: 'left', label: 'E-mail', field: 'Email', sortable: true },
      { name: 'Phone', align: 'right', label: 'Téléphone', field: 'Phone', sortable: true },
      { name: 'Action', align: 'center', label: 'Actions', field: 'Action' }
    ]
    export default {
      data () {
        const addStaff = () => {
        var form = new FormData()
        form.append('name', this.name)
        form.append('position', this.position)
        form.append('email', this.email)
        form.append('phone', this.phone)
        form.append('idNGO', this.NGOId)
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
          showEditStaff: false,
          idStaff: null,
          name: null,
          position: null,
          email: null,
          phone: null,
          filter: '',
          staffs: [],
          options:[
                    {
                      value: 'Program Manager',
                      label: 'Manager Programme'
                    },
                    {
                      label: 'Manager Projet',
                      value: 'Project Manager'
                    },
                    {
                      label: 'Superviseur des AVEC',
                      value: 'VSLA Manager'
                    }
                  ],
          userId: this.$session.get(1),
          username: this.$session.get(2),
          typeUser: this.$session.get(3),
          emailUser: this.$session.get(4),
          userphone: this.$session.get(5),
          NGOId: this.$session.get(11),
          StaffId: this.$session.get(12),
          NGOName: this.$session.get(13)
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
                ngoId: this.NGOId
              }
          }).then((dataFront) => {
              this.staffs = dataFront.data
          })
        },
        editStaff(id, name, position, email, phone) {
          this.idStaff = id
          this.name = name
          this.position = position
          this.email = email
          this.phone = phone
          this.showEditStaff = true
        }
      },
      components: {
        'edit-staff': require('components/client/EditStaff.vue').default
      }
    }
    </script>
    