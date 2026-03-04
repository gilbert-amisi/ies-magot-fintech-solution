<template>
    <q-page>
        <div class="q-pa-md">
            <q-layout view="hHh Lpr lff" container flat style="height: 600px" class="shadow-2 rounded-borders">
                <q-header elevated class="bg-grey-2 text-light-blue-10">
                    <q-toolbar>
                        <q-toolbar-title class="text-bold text-h6" style="font-family: Helvetica;">GESTION DES AVEC - {{ NGOName.toUpperCase() }}</q-toolbar-title>
                        <q-tabs v-model="tab" shrink inline-label>
                            <q-btn v-if="typeUser == 'Program Manager' || typeUser == 'Project Manager'" stretch label="Ajouter une AVEC" @click="showAddVsla=true" icon="add_home_work" color="light-blue-10"/> <q-separator vertical />
                            <q-btn to="/vsladash" stretch label="Tableau de bord" icon="dashboard" color="accent"/>
                        </q-tabs>
                        
                    </q-toolbar>
                </q-header>
                <q-page-container>
                    <q-page class="q-pa-sm">
                        <q-table bordered separator="cell"
                            :data="vslas"
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
                                    {{ props.row.Type }} {{ props.row.label }}
                                </q-td>
                                <q-td key="Title" :props="props">
                                    {{ props.row.Title }}
                                </q-td>
                                <q-td key="Location" :props="props">
                                <div class="text-pre-wrap"> {{ props.row.Country }}, {{ props.row.Province }}, {{ props.row.Location }} </div>
                                </q-td>
                                <q-td key="Start" :props="props">
                                    {{ props.row.Start }}
                                </q-td>
                                <q-td key="Nominalvalue" :props="props">
                                    {{ props.row.Devise }} {{ props.row.Nominalvalue }}
                                </q-td>
                                <q-td key="Plafond" :props="props">
                                    % {{ props.row.CreditRate }}
                                </q-td>
                                <q-td key="Seuil" :props="props">
                                    {{ props.row.Devise }} {{ props.row.Seuil }}
                                </q-td>
                                <q-td key="Staff" :props="props">
                                    {{ props.row.Staff }}
                                </q-td>
                                
                                <q-td key="Action" :props="props">
                                    <div class="col-auto">
                                        <q-btn color="grey-7" dense flat no-caps icon="more_vert" label="Plus d'options" text-color="accent">
                                            <q-menu cover auto-close>
                                                <q-list>
                                                    <q-item-label overline header class="text-bold text-center"> 
                                                        {{ props.row.Type }} {{ props.row.label }}
                                                    </q-item-label>
                                                    <q-separator></q-separator>
                                                    <q-item clickable v-ripple @click="getMembers(props.row.value, props.row.label, props.row.Type)">
                                                        <q-item-section side>
                                                            <q-icon name="group" color="accent"></q-icon>
                                                        </q-item-section>
                                                        <q-item-section>Gérer les membres</q-item-section>
                                                    </q-item>
                                                    <q-item clickable v-ripple @click="getAccounts(props.row.value, props.row.label, props.row.Type)">
                                                        <q-item-section side>
                                                            <q-icon name="balance" color="cyan-10"></q-icon>
                                                        </q-item-section>
                                                        <q-item-section>Numéros des comptes</q-item-section>
                                                    </q-item>
                                                    <q-item clickable v-ripple @click="editVSLA(props.row.value, props.row.label, props.row.Type, props.row.ProjectId, props.row.Country , props.row.Province , props.row.Location, props.row.StaffId, props.row.Devise,  props.row.Start, props.row.Nominalvalue, props.row.CreditRate, props.row.Seuil)">
                                                        <q-item-section side>
                                                            <q-icon name="drive_file_rename_outline" color="light-blue-10"></q-icon>
                                                        </q-item-section>
                                                    <q-item-section>Editer les informations</q-item-section>
                                                    </q-item>
                                                    <q-item clickable v-ripple @click="deleteVSLA(props.row.value)">
                                                        <q-item-section side>
                                                            <q-icon name="close" color="negative"></q-icon>
                                                        </q-item-section>
                                                        <q-item-section>Désactiver l'AVEC</q-item-section>
                                                    </q-item>
                                                </q-list>
                                            </q-menu>
                                        </q-btn>
                                    </div>

                                   
                                </q-td>   
                            </q-tr>
                            </template>
                            <template v-slot:top-left>
                                <q-input filled dense debounce="500" v-model="filter" placeholder="Rechercher une AVEC" class="col">
                                <template v-slot:append>
                                    <q-icon name="search" />
                                </template>
                                </q-input>
                            </template>
                            
                            <template v-slot:top-right v-if="typeUser == 'Program Manager' || typeUser == 'Project Manager'">
                                
                                <div class="full-width">
                                   
                                    <div style="min-width: 450px; max-width: 500px" class="row mt-4 flex flex-center">
                                        <q-item-label class="col-md-3 text-subtitle2 text-bold text-light-blue-10">Filtrer par projet</q-item-label>
                                        <q-select class="col-md-6" dense  :options="projects" v-model="projectId" 
                                             emit-value ref="projectId" map-options @input="listprojectvslas"
                                            >
                                        </q-select>
                                        
                                    </div>
                                    </div>
                               
                                
                            </template>

                        </q-table>
                    </q-page>
                </q-page-container>
            </q-layout>
            <q-dialog v-model="showAddVsla" persistent>
                <add-vsla></add-vsla>
            </q-dialog>
            <q-dialog v-model="showEditVsla" persistent>
                <edit-vsla :idVSLA="idVSLA" :nameV="nameVSLA" :typeV="typeVSLA" :projectV="projectId" :countryV="country" :provinceV="provinceId" :locationV="location" :startV="start" :deviseV="devise" :nominalV="nominalvalue" :creditrateV="creditrate" :seuilV="seuil" :staffV="staffId"></edit-vsla>
            </q-dialog>
            <q-dialog v-model="showMembers" persistent>
                <show-members :idVSLA="idVSLA" :nameVSLA="nameVSLA" :typeVSLA="typeVSLA"></show-members>
            </q-dialog>
            <q-dialog v-model="showAccounts" persistent>
                <show-accounts :idVSLA="idVSLA" :nameVSLA="nameVSLA" :typeVSLA="typeVSLA"></show-accounts>
            </q-dialog>
        </div>
    </q-page>
</template>

<script>
import Vue from 'vue'
import axios from 'axios'
// import VueApexCharts from 'vue-apexcharts'
import mysession from 'vue-session'
Vue.use(mysession)

// Vue.component('apexchart', VueApexCharts)
const columns = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
        {
          name: 'label',
          required: true,
          label: 'Name of Group',
          align: 'left',
          field: row => row.label,
          format: val => `${val}`,
          sortable: true
        },
        { name: 'Title', align: 'left', label: 'Projet', field: 'Title', sortable: true },
        { name: 'Location', align: 'left', label: 'Localisation', field: 'Location', sortable: true  },
        { name: 'Start', align: 'right', label: 'Date de création', field: 'Start', sortable: true  },
        { name: 'Nominalvalue', align: 'right', label: 'Valeur nominale', field: 'Nominalvalue', sortable: true  },
        { name: 'Plafond', align: 'right', label: "Taux d'emprunt maximal", field: 'Plafond', sortable: true  },
        { name: 'Seuil', align: 'right', label: 'Seuil de liquidité', field: 'Seuil', sortable: true  },
        { name: 'Staff', align: 'left', label: 'Superviseur', field: 'Staff', sortable: true },
        { name: 'Action', align: 'center', label: 'Actions', field: 'Action' }
        
    ]
export default {
    data () {
        return {
            drawer: false,
            miniState: true,
            showAddVsla: false,
            showEditVsla: false,
            showMembers: false,
            showAccounts: false,
            vslas: [],
            projects: [],
            idVSLA: null,
            nameVSLA: null,
            typeVSLA: null,
            projectId: null,
            staffId: null,
            devise: null,
            country: null,
            provinceId : null,
            location: null,
            start: null,
            nominalvalue: null,
            creditrate: null,
            seuil: null,
            columns,
            tab: 'list',
            filter: '',
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
        this.listProject()
        this.listVslaAll()
    },
    methods: {
        listVslaAll () {
            if (this.typeUser === 'VSLA Manager') {
                axios.get(this.$urlServer + 'vslaControllers/listVSLAPerStaff.php', {
                params: {
                    staffId: this.$session.get(12)
                }
                }).then((dataFront) => {
                    this.vslas = dataFront.data
                })
            } else {
                axios.get(this.$urlServer + 'vslaControllers/listVSLA.php', {
                params: {
                    ngoId: this.NGOId
                }
                }).then((dataFront) => {
                    this.vslas = dataFront.data
                })
            }
            
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
        listprojectvslas () {
            axios.get(this.$urlServer + 'vslaControllers/listVSLAPerProject.php', {
                params: {
                    projectId: this.projectId
                }
            }).then((dataFront) => {
                this.vslas = dataFront.data
            })
        },
        getMembers (id, name, type) {
            this.idVSLA = id
            this.nameVSLA = name
            this.typeVSLA = type
            this.showMembers= true
        },
        getAccounts (id, name, type) {
            this.idVSLA = id
            this.nameVSLA = name
            this.typeVSLA = type
            this.showAccounts= true
        },
        editVSLA(id, name, type, projectId, country, province, location, staffId, devise,  start, nominal, creditrate, seuil) {
            this.idVSLA = id
            this.nameVSLA = name
            this.typeVSLA = type
            this.projectId = projectId
            this.provinceId = province
            this.location = location
            this.staffId = staffId
            this.devise = devise
            this.country = country
            this.start = start
            this.nominalvalue = nominal
            this.creditrate = creditrate
            this.seuil = seuil
            this.showEditVsla= true
        },
        deleteVSLA (id) {
        this.$q.dialog({
            title: 'Confirm Deletion',
            message: 'Do you really want to delete this Group ? \n Note that Group deletion includes deletion of all its members and accounts',
            ok: {
                push: true
            },
            cancel: {
                color: 'negative'
            },
            persistent: true
        }).onOk(() => {
            axios.get(this.$urlServer + 'clientControllers/deleteVSLA.php', {
              params: {
                clientId: id
              }
                
            }).then((dataFront) => {
                this.$q.dialog({
                    title: 'Notification',
                    message: 'Group successfully deleted !',
                    ok: {
                    push: true
                    },
                    persistent: true
                })
                this.listvslas()
                })
            })
        }
    },
    components: {
        'add-vsla': require('components/vsla/AddVSLA.vue').default,
        'edit-vsla': require('components/vsla/EditVSLA.vue').default,
        'show-members': require('components/vsla/Members.vue').default,
        'show-accounts': require('components/vsla/Accounts.vue').default
    }
}
</script>