<template>
    <q-page class="q-pa-xs" ref="document">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
              <div class="row">
                <!-- <q-item-label class="text-subtitle2 q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label> -->
                    <q-select label="Filtrer par cycle" label-color="light-blue-10" @input="getReport" class="col" dense standard :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value></q-select>    
                <q-space></q-space>
                <q-btn
                      color="light-blue-10"
                      icon-right="archive"
                      label="Télécharger"
                      no-caps @click="print"
                  />
            </div>
        </q-card-section>
        <q-separator></q-separator>
        <q-card-section id="PrintMe">
          <q-markup-table flat bordered separator="cell">
              <thead class="bg-grey-3">
                <tr>
                  <th colspan="3">
                    <div class="text-h6 text-light-blue-10 text-center">GROUPE {{ vslaName }}</div>
                    <q-separator></q-separator>
                    <div class="row">
                      <div v-if="cycleFilter == null" class="text-subtitle2 text-bold text-light-blue-10 text-center">RAPPORT FINANCIER GLOBAL</div>
                      <div v-if="cycleFilter != null" class="text-subtitle2 text-bold text-light-blue-10 text-center">RAPPORT FINANCIER DU GROUPE AU {{ this.cycles.find(option => option.value === this.cycleFilter).label.toUpperCase() }}</div>
                      <q-space></q-space>
                      <q-badge rounded outline class="text-subtitle2 text-bold" color="orange">{{ vslaCurrency }}</q-badge>
                    </div>
                  </th>
                </tr>
                <tr>
                  <th class="text-right">Opérations</th>
                  <th class="text-right">Entrée (+)</th>
                  <th class="text-right">Sortie (-)</th>
                </tr>
              </thead>
              <tbody class="bg-grey-1">
                <tr>
                  <td class="text-right">Epargnes encaisées</td>
                  <td class="text-right">{{ this.data[0].Epargne }}</td>
                  <td class="text-right">-</td>
                </tr>
                <tr>
                  <td class="text-right">Contributions sociales</td>
                  <td class="text-right">{{ this.data[0].FS}}</td>
                  <td class="text-right">-</td>
                </tr>
                <tr>
                  <td class="text-right">Dons et Legs reçus</td>
                  <td class="text-right">{{ this.data[0].Dons }}</td>
                  <td class="text-right">-</td>
                </tr>
                <tr>
                  <td class="text-right">Penalités sur membres</td>
                  <td class="text-right">{{ this.data[0].Penalites }}</td>
                  <td class="text-right">-</td>
                </tr>
                <tr>
                  <td class="text-right">Crédits octroyés</td>
                  <td class="text-right">-</td>
                  <td class="text-right">{{ this.data[0].Credit }}</td>
                </tr>
                <tr>
                  <td class="text-right">Crédits remboursés</td>
                  <td class="text-right">{{ this.data[0].Remboursement }}</td>
                  <td class="text-right">-</td>
                </tr>
                <tr>
                  <td class="text-right">Dépenses et Assistances</td>
                  <td class="text-right">-</td>
                  <td class="text-right">{{ this.data[0].Depenses }}</td>
                </tr>
                <tr>
                  <td class="text-right">Capitaux partagés</td>
                  <td class="text-right">-</td>
                  <td class="text-right">{{ this.data[0].Partage }}</td>
                </tr>
                <tr class="text-bold">
                  <td class="text-right">Total ({{ vslaCurrency }})</td>
                  <td class="text-right">{{ this.data[0].Actif }}</td>
                  <td class="text-right">{{ this.data[0].Passif }}</td>
                </tr>
                
              </tbody>
              <tfoot align="center">
                <div class="q-pa-sm" >
                    <div class="text-subtitle2 text-overline text-center">IES MAGOT @iescongo</div>
                    </div>
              </tfoot>
                    
          </q-markup-table>  
        </q-card-section>
            
      </q-card>
          
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'
  import Vue from 'vue';
  import VueHtmlToPaper from 'vue-html-to-paper';
  Vue.use(VueHtmlToPaper, options);

  const options = {
      name: '_blank',
      specs: [
        'fullscreen=yes',
        'titlebar=yes',
        'scrollbars=yes'
      ],
      styles: [
        'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
        'https://unpkg.com/kidlat-css/css/kidlat.css'
      ],
      timeout: 100000, // default timeout before the print window appears
      autoClose: true, // if false, the window will not close after printing
      windowTitle: window.document.title, // override the window title
    }
  export default {
    data () {
      return {
        cycleFilter: null,
        cycles: [],
        data: [],
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8),
        vslaCurrency: this.$session.get(10)
      }
    },
    created () {
      this.listCycles()
      this.globalreport()
    },
    methods: {
        listCycles () {
            axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.cycles = dataFront.data
            })
        },
        getReport () {
            axios.get(this.$urlServer + 'dashboardControllers/rapportFinancierDash.php', {
                params: {
                  cycleId: this.cycleFilter
                }
            }).then((dataFront) => {
              this.data = dataFront.data
            }) 
        },
  
        globalreport () {
          axios.get(this.$urlServer + 'dashboardControllers/rapportFinancierAllDash.php', {
              params: {
                vslaId: this.vslaId
              }
          }).then((dataFront) => {
            this.data = dataFront.data
          })
        },
        print () {
          this.$htmlToPaper('PrintMe');
      // Pass the element id here
          // VueHtml2pdf(document.getElementById('PrintMe'), {
          //   margin: 1,
          //   filename: 'Rapport financier' + this.vslaName +'.pdf'
          // });
        },
  
    }
  
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    