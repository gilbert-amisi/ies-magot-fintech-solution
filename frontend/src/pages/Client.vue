<template>
    <q-page>
        <div class="q-pa-md">
            <q-layout view="hHh Lpr lff" container flat style="height: 600px" class="shadow-2 rounded-borders">
                <q-header elevated class="bg-grey-2 text-light-blue-10">
                    <q-toolbar>
                        <q-toolbar-title class="text-bold text-h6">CUSTOMER NGOs MANAGEMENT</q-toolbar-title>
                        <q-tabs v-model="tab" shrink inline-label>
                            <q-tab name="list" label="List of NGOs" icon="handshake" class="text-teal"/>
                            <q-tab name="subscriptions" label="subscriptions" icon="subscriptions" class="text-negative"/>
                        </q-tabs>
                        
                    </q-toolbar>
                </q-header>
                <q-page-container>
                    <q-page>
                        <q-tab-panels v-model="tab" animated>
                            <q-tab-panel name="list">

                                <q-table bordered separator="cell"
                                    :data="organizations"
                                    :columns="columnsngo"
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
                                        <q-td key="Website" :props="props">
                                        {{ props.row.Website }}
                                        </q-td>
                                        <q-td key="Staff" :props="props">
                                            <q-btn @click="getStaff(props.row.value, props.row.label)" side flat no-caps dense color="accent" icon="badge" label="Manage users" ></q-btn>
                                        </q-td>

                                        <q-td key="Action" :props="props">
                                            <q-btn side no-caps flat dense color="accent" icon="edit_notifications" title="Add subscription" ></q-btn>
                                            <q-btn side no-caps flat dense color="primary" icon="edit" title="Edit client" ></q-btn>
                                            <q-btn side no-caps flat dense color="red" icon="delete" title="Delete  client"></q-btn>
                                        </q-td>   
                                    </q-tr>
                                    </template>
                                    <template v-slot:top-right >
                                        <q-btn color="primary" no-caps push icon="add_moderator" label="Add New NGO" @click="showAddOrg=true" /><q-separator />
                                    </template>

                                    <template v-slot:top-left>
                                        <q-input filled dense debounce="500" v-model="filter" placeholder="Search NGO" class="col">
                                        <template v-slot:append>
                                            <q-icon name="search" />
                                        </template>
                                        </q-input>
                                    </template>

                                </q-table>

                            </q-tab-panel>
                            <q-tab-panel name="subscriptions">
                                <q-table bordered separator="cell" title="Clients subscriptions"
                                    :data="subscriptions"
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
                                        <q-td key="Client" :props="props">
                                        {{ props.row.Client }}
                                        </q-td>
                                        <q-td key="Start" :props="props">
                                        {{ props.row.Start }}
                                        </q-td>
                                        <q-td key="End" :props="props">
                                            {{ props.row.End }}
                                        </q-td>
                                        <q-td key="Duration" :props="props">
                                        <div class="text-pre-wrap">{{ props.row.Duration }} years</div>
                                        </q-td>
                                        <q-td key="Level" :props="props">
                                            {{ props.row.Level }}
                                        </q-td>
                                        <q-td key="Maxnumber" :props="props">
                                            {{ props.row.Maxnumber }}
                                        </q-td>
                                        <q-td key="Status" :props="props">
                                            {{ props.row.Status }}
                                        </q-td>
                                        <q-td key="Action" :props="props">
                                        <q-btn @click="disableSubscription(props.row.value)" v-if="props.row.Status=='Active'" flat dense icon="close" color="red" title="Disable subscription"></q-btn>
                                        <q-btn @click="enableSubscription(props.row.value)" v-if="props.row.Status!='Active'" flat dense icon="refresh" color="teal" title="Enable subscription"></q-btn>
                                        <q-btn @click="editSubscription(props.row.value, props.row.Start, props.row.End, props.row.Level, props.row.Maxnumber)" side flat dense icon="edit" color="primary" title="Edit indicator"></q-btn>
                                        <q-btn @click="deleteSubscription(props.row.value)" side flat dense icon="delete" color="negative" title="Delete indicator"></q-btn>
                                        </q-td>   
                                    </q-tr>
                                    </template>
                                    <template v-slot:top-right>
                                        <q-input filled dense debounce="500" v-model="filter" placeholder="Search" class="col">
                                        <template v-slot:append>
                                            <q-icon name="search" />
                                        </template>
                                        </q-input>
                                    </template>

                                </q-table>
                            </q-tab-panel>

                        </q-tab-panels>
                    </q-page>
                </q-page-container>
            </q-layout>
            
            <q-dialog v-model="showAddOrg" persistent>
                <add-org></add-org>
            </q-dialog>
            <q-dialog v-model="showStaff" persistent>
                <show-staff :idClient="idClient" :nameClient="nameClient"></show-staff>
            </q-dialog>
            <q-dialog v-model="showAddSubscription" persistent>
                <add-subscription :idClient="idClient" :nameClient="nameClient"></add-subscription>
            </q-dialog>
            <q-dialog v-model="showEditOrg" persistent>
                <edit-org :idClient="idClient" :nameClient="nameClient" :website="website" :idmanager="idmanager" :namemanager="namemanager" :emailmanager="emailmanager" :phonemanager="phonemanager"></edit-org>
            </q-dialog>
            <q-dialog v-model="showEditSubscription" persistent>
                <edit-subscription :idSubscription="idSubscription" :start="start" :end="end" :level="level" :maxnumber="maxnumber"></edit-subscription>
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
const columnsngo = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
        { name: 'label', required: true, label: 'Name of NGO', align: 'left', field: row => row.label, format: val => `${val}`, sortable: true },
        { name: 'Website', align: 'left', label: 'Website', field: 'Website', sortable: true },
        { name: 'Staff', align: 'center', label: 'Staffs & Users', field: 'Staff', sortable: true },
        { name: 'Action', align: 'center', label: 'Actions', field: 'Action' }
        
    ]
    const columns = [
        { name: 'value', align: 'right', label: 'N°', field: 'value', sortable: true },
        {
          name: 'Client',
          required: true,
          label: 'Client',
          align: 'left',
          field: row => row.name,
          format: val => `${val}`,
          sortable: true
        },
        { name: 'Start', align: 'left', label: 'Start date', field: 'Start', sortable: true },
        { name: 'End', align: 'right', label: 'End date', field: 'End' },
        { name: 'Duration', label: 'Duration', field: 'Duration', sortable: true },
        { name: 'Level', label: 'Category', field: 'Level', sortable: true },
        { name: 'Maxnumber', label: 'Users (Max)', field: 'Maxnumber', sortable: true },
        { name: 'Status', label: 'Status', field: 'Status', sortable: true },
        { name: 'Action', align: 'center', label: 'Action', field: 'Action' }
        
    ]
export default {
    data () {
        return {
            drawer: false,
            miniState: true,
            showAddOrg: false,
            showEditOrg: false,
            showStaff: false,
            showAddSubscription: false,
            showEditSubscription: false,
            organizations: [],
            columns,
            columnsngo,
            subscriptions: [],
            tab: 'list',
            filter: '',
            idClient: null,
            nameClient: null,
            website: null, 
            idmanager: null,
            namemanager: null, 
            emailmanager: null, 
            phonemanager: null,
            idSubscription: null,
            start: null,
            end: null,
            level: null,
            maxnumber: null,
            series: [],
            chartOptions: {},
            userId: this.$session.get(1),
            username: this.$session.get(2),
            typeUser: this.$session.get(3),
            emailUser: this.$session.get(4),
            userphone: this.$session.get(5)
        }
    },
    created () {
        this.listOrganizations()
    },
    methods: {
        listOrganizations () {
            axios.get(this.$urlServer + 'ngoControllers/listNGO.php', {
            }).then((dataFront) => {
                this.organizations = dataFront.data
            })
        },
        listSubscriptions () {
            axios.get(this.$urlServer + 'subscriptionControllers/listSubscription.php', {
            }).then((dataFront) => {
                this.subscriptions = dataFront.data
            })
        },
        getStaff (idClient, nameClient) {
            this.idClient = idClient
            this.nameClient = nameClient
            this.showStaff = true
        },
        subscribe (idClient, nameClient) {
            this.idClient = idClient
            this.nameClient = nameClient
            this.showAddSubscription= true
        },
        editClient(idClient, nameClient, website, idmanager, namemanager, emailmanager, phonemanager) {
            this.idClient = idClient
            this.nameClient = nameClient
            this.website = website
            this.idmanager = idmanager
            this.namemanager = namemanager
            this.emailmanager = emailmanager
            this.phonemanager = phonemanager
            this.showEditOrg= true
        },
        editSubscription(idSubscription, start, end, level, maxnumber) {
            this.idSubscription = idSubscription
            this.start = start
            this. end = end
            this.level = level
            this.maxnumber = maxnumber
            this.showEditSubscription= true
        },
        deleteClient (id) {
        this.$q.dialog({
            title: 'Confirm Deletion',
            message: 'Do you really want to delete this client ? \n Note that client deletion includes deletion of all its programs and projects',
            ok: {
                push: true
            },
            cancel: {
                color: 'negative'
            },
            persistent: true
        }).onOk(() => {
            axios.get(this.$urlServer + 'clientControllers/deleteClient.php', {
              params: {
                clientId: id
              }
                
            }).then((dataFront) => {
                this.$q.dialog({
                    title: 'Notification',
                    message: 'Subscription successfully deleted !',
                    ok: {
                    push: true
                    },
                    persistent: true
                })
                this.listOrganizations()
                })
            })
        },
        disableSubscription(id) {
            this.$q.dialog({
                title: 'Confirm Closing',
                message: 'Do you really want to disable this subscription ?',
                ok: {
                    push: true
                },
                cancel: {
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                axios.get(this.$urlServer + 'subscriptionControllers/changeStatusSubscription.php', {
                params: {
                    subscriptionId: id,
                    status: 'Expired'
                }
                    
                }).then((dataFront) => {
                this.$q.dialog({
                    title: 'Notification',
                    message: 'Subscription successfully disabled !',
                    ok: {
                    push: true
                    },
                    persistent: true
                })
                this.listSubscriptions()
                })
            })
        },
        enableSubscription(id) {
            this.$q.dialog({
                title: 'Confirm Closing',
                message: 'Do you really want to enable this subscription ?',
                ok: {
                    push: true
                },
                cancel: {
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                axios.get(this.$urlServer + 'subscriptionControllers/changeStatusSubscription.php', {
                params: {
                    subscriptionId: id,
                    status: 'Active'
                }
                    
                }).then((dataFront) => {
                this.$q.dialog({
                    title: 'Notification',
                    message: 'Subscription successfully disabled !',
                    ok: {
                    push: true
                    },
                    persistent: true
                })
                this.listSubscriptions()
                })
            })
        },
        deleteSubscription (id) {
        this.$q.dialog({
            title: 'Confirm Deletion',
            message: 'Do you really want to delete this subscription ?',
            ok: {
                push: true
            },
            cancel: {
                color: 'negative'
            },
            persistent: true
        }).onOk(() => {
            axios.get(this.$urlServer + 'subscriptionControllers/deleteSubscription.php', {
              params: {
                subscriptionId: id
              }
                
            }).then((dataFront) => {
                this.$q.dialog({
                    title: 'Notification',
                    message: 'Subscription successfully deleted !',
                    ok: {
                    push: true
                    },
                    persistent: true
                })
                this.listSubscriptions()
                })
            })
        },
        statClients () {
            axios.get(this.$urlServer + 'clientControllers/usersClientDashboard.php', {
            }).then((dataFront) => {
                this.series = dataFront.data[0].series
                this.chartOptions = dataFront.data[0].chartOptions
            })
        }
    },
    components: {
        'add-org': require('components/client/AddClient.vue').default,
        'edit-org': require('components/client/EditClient.vue').default,
        'show-staff': require('components/client/Staff.vue').default,
        'add-subscription': require('components/client/AddSubscription.vue').default,
        'edit-subscription': require('components/client/EditSubscription.vue').default
    }
}
</script>