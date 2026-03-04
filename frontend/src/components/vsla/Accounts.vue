<template>
    <q-card style="width: 100%; max-width: 1200px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">Numéros des comptes de {{ typeVSLA.toUpperCase() }} {{ nameVSLA.toUpperCase() }} </q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <div class="row mt-4 q-gutter-sm q-pa-md">
        <q-card flat bordered class="col q-pa-md">
          <q-item-label header class="text-h6 text-dark text-bold">Ajouter un nouveau compte</q-item-label>
          <q-form>
            <div class="row q-mb-sm">
                <q-select use-input input-debounce="0" use-chips new-value-mode="add" dense outlined :options="operators" :rules="[myRule]"  v-model="operator" ref="operator" label="Opérateur" class="col" emit-value    
                >
                </q-select>
            </div>
            <div class="row q-mb-sm">
                <q-input dense outlined :rules="[myRule]"  v-model="intitule" ref="intitule" label="Intitulé du compte" class="col" emit-value    
                >
                </q-input>
            </div>
            <div class="row q-mb-sm">
              <q-input dense outlined :rules="[myRule]" v-model="phone" ref="phone" label="Numéro des comptes" class="col">
              </q-input>
            </div>
              
            <div align="right">
              <q-btn dense
                  @click="addAccount"
                  label="Enregistrer"
                  color="light-blue-10"
                  icon="save"
                  push
                  type="submit">
              </q-btn>
            </div>
          </q-form>
        </q-card>
        <q-table bordered flat separator="cell" class="col q-pa-sm"
              :data="accounts"
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

                  <q-td key="Operator" :props="props">
                      {{ props.row.Operator }}
                  </q-td>
                  <q-td key="Intitule" :props="props">
                      {{ nameVSLA }}
                  </q-td>
                  <q-td key="Numero" :props="props">
                      {{ props.row.Phone }}
                  </q-td>
                  <q-td key="Action" :props="props">
                      <q-btn side flat dense icon="edit" color="primary" title="Edit Acccount"></q-btn>
                      <q-btn side flat dense icon="delete" color="negative" title="Delete Account"></q-btn>
                  </q-td>   
              </q-tr>
              </template>
              <template v-slot:top-right>
                  <q-input filled dense debounce="500" v-model="filter" placeholder="Search Acount" class="col">
                  <template v-slot:append>
                      <q-icon name="search" />
                  </template>
                  </q-input>
              </template>
              <template v-slot:top-left>
                  <q-item-label class="text-h6 text-dark text-bold">Comptes enregistrés</q-item-label>
              </template>

        </q-table>
      </div>
    </q-card>
  </template>
  <script>
  import axios from 'axios'

  const columns = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
        { name: 'Operator', align: 'left', label: 'Opérateur mobile', field: 'Operator' },
        { name: 'Intitule', align: 'right', label: 'Intitulé du compte', field: 'Intitule' },
        { name: 'Numero', align: 'right', label: 'Numéro du compte', field: 'Numero' },
        { name: 'Action', align: 'center', label: 'Action', field: 'Action' }
        
    ]
  export default {
    props: ['idVSLA', 'nameVSLA', 'typeVSLA'],
    data () {
      const addAccount = () => {
      var form = new FormData()
      form.append('operator', this.operator)
      form.append('phone', this.phone)
      form.append('vslaId', this.idVSLA)
      if (this.operator !== null && this.phone !== null) {
          axios.post(this.$urlServer + 'accountControllers/addAccount.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Mobile Money Account of Group successfully saved'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listaccounts()
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
        addAccount,
        columns,
        operator: null,
        intitule: null,
        phone: null,
        filter: '',
        operators: ['Airtel Money', 'M-Pesa', 'Orange Money', 'MTN', 'Banque'],
        accounts: []
      }
    },
    created () {
        this.listaccounts()
    },
    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Required')
            }, 800)
            })
        },
        listaccounts () {
            axios.get(this.$urlServer + 'accountControllers/listAccount.php', {
                params: {
                    vslaId: this.idVSLA
                }
            }).then((dataFront) => {
                this.accounts = dataFront.data
            })
        }
    }
  }
  </script>
  