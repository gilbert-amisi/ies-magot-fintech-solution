<template>
    <q-page class="q-pa-xs">
        <q-card>
          <q-card-section class="bg-grey-1">
              <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">GESTION DES PROJETS - {{ NGOName.toUpperCase() }}</q-item-label>  
          </q-card-section>
          <q-separator horizontal/>
          <q-card-section>
            <div class="row mt-4">
            <q-card class="col-md-5 q-pa-sm" v-if="typeUser=='Program Manager'">
                <q-card-section>
                <q-item-label class="text-h6">Ajouter un projet</q-item-label>
                </q-card-section>
                
                <q-form class="full-width">
                  <div class="row q-mb-sm">
                      <q-input dense outlined v-model="title" ref="title" label="Titre du projet" :rules="[ myRule ]" class="col">
                      </q-input>
                  </div>
                
                  <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="start" mask="####-##-##" v-model="start" label="Date de début" :rules="[ myRule ]" class="col">
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
                    <q-input dense outlined aria-required="required" ref="end" mask="####-##-##" v-model="end" label="Date de fin" :rules="[ myRule ]" class="col">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy">
                            <q-date v-model="end" mask="YYYY-MM-DD"></q-date>
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                  </div>
                  <div class="row q-mb-sm">
                    <q-select dense outlined v-model="staffId" ref="staffId" label="Staff Responsable" class="col" 
                      :rules="[ myRule ]"
                      :options="staffs" 
                      map-options emit-value>
                    </q-select>
                  </div>
                  <div align="right">
                    <q-btn
                    @click="addProject"
                    label="Save project"
                    color="primary"
                    icon="save"
                    outline
                    type="submit">
                    </q-btn>
                  </div>
                </q-form>
            </q-card>
    
            <div class="col-md">
                <q-table bordered separator="cell"
                title="Liste des projets"
                :data="projects"
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
                        <q-td key="Start" :props="props">
                          {{ props.row.Start }}
                        </q-td>
                        <q-td key="End" :props="props">
                          {{ props.row.End }}
                        </q-td>
                        <q-td key="Status" :props="props">
                          {{ props.row.Status }}
                        </q-td>
                        <q-td key="Name" :props="props">
                          {{ props.row.Name }}
                        </q-td>
                        <q-td v-if="typeUser=='Program Manager'" key="Action" :props="props">
                          <q-btn side no-caps flat dense color="primary" icon="edit" title="Edit project" 
                            @click="editProject(props.row.value, props.row.label, props.row.Start, props.row.End, props.row.StaffId)" >
                          </q-btn>
                          
                          <q-btn v-if="props.row.Status=='Closed'" side no-caps flat dense color="accent" icon="lock_reset" title="Restart project" 
                            @click="restartProject(props.row.value)" >
                          </q-btn>
                          <q-btn v-if="props.row.Status=='In progress'" @click="closeProject(props.row.value)" side no-caps flat dense color="negative" icon="close" title="Close project"></q-btn>
                          <q-btn @click="deleteProject(props.row.value)" side no-caps flat dense color="red" icon="delete" title="Delete project"></q-btn>
                        </q-td>   
                    </q-tr>
                    </template>
                    <template v-slot:top-right>
                        <q-input filled dense debounce="500" v-model="filter" placeholder="Rechercher un projet" class="col">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                        </q-input>
                    </template>
    
                </q-table>

                <q-dialog v-model="showEditProject" persistent>
                  <edit-project :idProject="projectId" :titleP="title" :startP="start" :endP="end" :staffP="staffId"></edit-project>
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
      { name: 'label', required: true, label: 'Titre du projet', align: 'left', field: 'label', sortable: true },
      { name: 'Start', align: 'right', label: 'Date de début', field: 'Start', sortable: true },
      { name: 'End', align: 'right', label: 'Date de fin', field: 'End', sortable: true },
      { name: 'Status', align: 'left', label: 'Statut', field: 'Status', sortable: true },
      { name: 'Name', align: 'left', label: 'Responsable', field: 'Name', sortable: true },
      { name: 'Action', align: 'center', label: 'Actions', field: 'Action' }
    ]
    export default {
      data () {
        const addProject = () => {
        var form = new FormData()
        form.append('title', this.title)
        form.append('start', this.start)
        form.append('end', this.end)
        form.append('status', this.status)
        form.append('staffId', this.staffId)
        if (this.title !== null && this.start !== null && this.end !== null && this.staffId !== null) {
            axios.post(this.$urlServer + 'projectControllers/addProject.php', form, {
            }).then((res) => {
              this.$q.dialog({
                title: 'Success',
                message: ('Project successfully saved'),
                ok: {
                  push: true
                },
                cancel: {
                  color: 'negative'
                },
                persistent: true
              }).onOk(() => {
                this.listProject()
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
          addProject,
          projectId: null,
          title: null,
          staffId: null,
          start: null,
          end: null,
          status: 'In progress',
          filter: '',
          staffs: [],
          projects: [],
          showEditProject: false,
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
        this.listProject()
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
          axios.get(this.$urlServer + 'staffControllers/listStaffPM.php', {
            params: {
              ngoId: this.NGOId
            }
          }).then((dataFront) => {
            this.staffs = dataFront.data
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
        editProject (id, title, start, end, staffId) {
          this.projectId = id
          this.title = title
          this.start = start
          this.end = end
          this.staffId = staffId
          this.showEditProject=true
        },
        closeProject (id) {
          this.$q.dialog({
            title: 'Success',
            message: ('Are sure you want to close this project?'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            axios.get(this.$urlServer + 'projectControllers/changeStatusProject.php', {
              params: {
                projectId: id,
                status: 'Closed'
              }
            }).then((res) => {
              this.$q.dialog({
                title: 'Success',
                message: ('Project successfully closed'),
                ok: {
                  push: true
                },
                cancel: {
                  color: 'negative'
                },
                persistent: true
              })
              this.listProject()
            })
          })
        },
        restartProject (id) {
          this.$q.dialog({
            title: 'Success',
            message: ('Are sure you want to restart this project?'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            axios.get(this.$urlServer + 'projectControllers/changeStatusProject.php', {
              params: {
                projectId: id,
                status: 'In progress'
              }
            }).then((res) => {
              this.$q.dialog({
                title: 'Success',
                message: ('Project successfully restarted'),
                ok: {
                  push: true
                },
                cancel: {
                  color: 'negative'
                },
                persistent: true
              })
              this.listProject()
            })
          })
        },
        deleteProject (id) {
          this.$q.dialog({
            title: 'Success',
            message: ('Are sure you want to delete this project?'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            axios.get(this.$urlServer + 'projectControllers/deleteProject.php', {
              params: {
                projectId: id,
              }
            }).then((res) => {
              this.$q.dialog({
                title: 'Success',
                message: ('Project successfully deleted'),
                ok: {
                  push: true
                },
                cancel: {
                  color: 'negative'
                },
                persistent: true
              })
              this.listProject()
            })
          })
        }
      },
      components: {
        'edit-project': require('components/client/EditProject.vue').default
      }
    }
    </script>
    