<template>
  <div class="q-pa-md">
    <q-layout view="hHh Lpr lff" container style="height: 850px" class="shadow-2 rounded-borders">
      <q-header elevated class="bg-grey-3">
        <q-toolbar>
          <q-btn flat @click="drawer = !drawer" round dense icon="dashboard" color="negative" />
          <q-toolbar-title v-if="group != null" class="text-h6 text-light-blue-10 text-bold" style="font-family: Helvetica">{{ this.vslas.find(option => option.value === this.group).Type + ' ' + this.vslas.find(option => option.value === this.group).label }}</q-toolbar-title>
          <q-toolbar-title v-if="group == null" class="text-h6 text-light-blue-10 text-bold" style="font-family: Helvetica">TABLEAU DE BORD DES AVEC</q-toolbar-title>
          <q-space />
          <q-tabs v-model="tab" shrink inline-label>
            <q-tab name="wallet" label="Portefeuille" icon="wallet" class="text-primary"/>
            <q-tab name="indicators" label="Indicateurs" icon="flag" class="text-accent" />
          </q-tabs>
        </q-toolbar>
      </q-header>

      <q-footer elevated class="bg-grey-3">
        <q-toolbar>
          <q-toolbar-title header class="text-h6 text-center text-cyan-10" style="font-family: Helvetica">Powered by IES CONGO</q-toolbar-title>
        </q-toolbar>
      </q-footer>

      <q-drawer
        v-model="drawer"
        :width="250"
        :breakpoint="500"
        show-if-above
        bordered
        content-class="bg-grey-3"
        style="font-size: 16px;"
      >
        <q-scroll-area class="fit">
          <q-list separator>
            
            <div class="q-pa-sm bg-grey-1" v-if="typeUser == 'Program Manager' || typeUser == 'Project Manager'">
              <q-select dense  :options="projects" v-model="projectId" label="FILTRER PAR PROJET" 
                emit-value ref="projectId" map-options @input="listprojectvslas"
                >
              </q-select>
            </div>
              <q-option-group
                :options="vslas"
                type="radio"
                name="group"
                ref="group"
                color="red"
                v-model="group"
                @input="showDashboard"
                value=""
                />

            <q-separator />

          </q-list>
        </q-scroll-area>
      </q-drawer>

      <q-page-container>
        <q-page>
          <q-tab-panels v-model="tab" animated>
            <q-tab-panel name="wallet">
              <q-card v-if="group == null">
                  <div class="text-h5 text-bold text-center text-negative q-pa-md"> Veillez sélectionner une AVEC à gauche pour la visualiser</div>
                </q-card>
              <q-card class="my-card" flat bordered v-if="group != null">
                  <div class="row">
                      
                      <div class="text-h6 text-bold text-light-blue-10 q-pa-sm">PORTEFEUILLE DE L'AVEC (EN {{ this.vslas.find(option => option.value === this.group).Devise }})</div>
                      <q-space />
                      <div v-if="group != null" class="text-overline q-pa-sm">
                        <q-btn flat no-caps color="primary" icon="place">
                          {{ this.vslas.find(option => option.value === this.group).Province }} / {{ this.vslas.find(option => option.value === this.group).Location }}
                        </q-btn>
                      </div>
                  
                  </div>
  
                  <q-separator />
  
                  <q-card-section class="q-pa-xs" dense>
                    <div class="row mt-4">
                      <q-card class="rounded-borders q-pa-xs bg-grey-2 col-md-4" separator style="height: 280px;">
                        <q-item class="full-width">
                          <apexchart class="full-width" width="100%" height="260" type="bar" :options="chartOptionsRequest" :series="seriesRequest"></apexchart>
                        </q-item>
                      </q-card>

                      <q-card separator style="height: 280px;" class="rounded-borders q-pa-xs bg-grey-2 col-md-5" >
                        <q-item class="full-width">
                          <apexchart class="full-width" width="100%" height="260" type="bar" :options="chartOptionsLoans" :series="seriesLoans"></apexchart>
                        </q-item>
                      </q-card>

                      <q-card class="rounded-borders q-pa-xs bg-grey-2 col-md-3" style="height: 280px;">
                        <q-item class="full-width" v-if="chartOptionsUses.labels.length > 0">
                          <apexchart width="100%" height="300" type="pie" :options="chartOptionsUses" :series="seriesUses"></apexchart>   
                        </q-item>
                      </q-card>

                    </div>
                  </q-card-section>

                  <q-separator />

                  <q-card-section class="q-pa-xs" dense>
                    <div class="row mt-4">
                      <q-card bordered class="rounded-borders q-pa-sm col-md-4" style="height: 280px;">
                        <apexchart type="line" width="100%" height="250" :options="chartOptionsFunds" :series="seriesFunds"></apexchart>
                      </q-card>

                      <q-card class="rounded-borders q-pa-sm col-md-3 bg-cyan-1" style="height: 280px;"> 
                        <apexchart type="radialBar" width="100%" height="250" :options="chartOptions1" :series="seriesSocialrate"></apexchart>   
                      </q-card> 

                      <q-card class="rounded-borders q-pa-sm col-md-5" style="height: 280px;">                   
                        <apexchart type="bar"  width="100%" height="250"  :options="chartOptionsSocial" :series="seriesSocial"></apexchart>
                      </q-card> 

                    </div>
                  </q-card-section>

                  <q-card-section class="q-pa-xs" dense>
                    <div class="row flex flex-center">
                      <q-item-label class="text-h6 q-pa-sm text-light-blue-10">Filtrer par cycle</q-item-label>
                      <div class="col q-pa-xs" align="left">
                        <q-select @input="dashCycle" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" map-options emit-value    
                          >
                        </q-select>
                      </div>
                    </div>
                    <div class="row mt-4">

                      <q-card class="rounded-borders bg-teal-1 q-pa-xs col-md-3">
                        <q-toolbar class="bg-light-blue-10 text-white shadow-2">
                            <q-toolbar-title>Crédits en cours (en {{ this.vslas.find(option => option.value === this.group).Devise }})</q-toolbar-title>
                        </q-toolbar>

                        <q-separator />
                        <div class="q-gutter-smc box bg-teal-1 shadow mt-4">
                          <q-stepper class="q-pa-xs bg-teal-1"
                            v-model="step"
                            header-nav
                            vertical
                            color="primary"
                            animated flat
                          >
                            <q-step
                              :name="1"
                              title="Montant total"
                              icon="money"
                              :done="step > 1"
                            >
                              <q-item-label class="text-h4 text-bold text-light-blue-10">{{ totalout }}</q-item-label>
                              
                            </q-step>

                            <q-step
                              :name="2"
                              title="Crédits sains"
                              icon="create_new_folder"
                              :done="step > 2"
                            >
                              <q-item-label class="text-h4 text-bold text-light-blue-10">{{ ontime }}</q-item-label>
                              
                            </q-step>

                            <q-step
                              :name="3"
                              title="Crédits en souffrance"
                              icon="assignment"
                            >
                              <q-item-label class="text-h4 text-bold text-light-blue-10">{{ overdue }}</q-item-label>
                              
                            </q-step>

                            </q-stepper>
                        </div>
                      </q-card>

                      <q-card class="rounded-borders q-pa-xs col-md-4">
                        <div class="box shadow mt-4">
                          <q-toolbar class="bg-light-blue-10 text-white shadow-2">
                            <q-toolbar-title>Fonds communs de l'AVEC (en {{ this.vslas.find(option => option.value === this.group).Devise }})</q-toolbar-title>
                          </q-toolbar>
                          <q-separator />
                            <q-item clickable v-ripple style="border-right: solid rgb(30, 85, 16) 10px;"> 
                              <q-item-section>
                                Dons et legs
                              </q-item-section>
                              <q-item-section side>
                                <q-item-label class="text-h6 text-bold text-grey"> {{ TotalDons }}</q-item-label>
                              </q-item-section>
                            </q-item>
                            <q-separator />

                            <q-item clickable v-ripple style="border-right: solid rgb(9, 27, 118) 10px;"> 
                              <q-item-section>
                                Fonds social
                              </q-item-section>
                              <q-item-section side>
                                <q-item-label class="text-h6 text-bold text-grey">{{ TotalSocial }}</q-item-label>
                              </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-item clickable v-ripple style="border-right: solid orange 10px;"> 
                              <q-item-section>
                                Penalités et Amendes
                              </q-item-section>
                              <q-item-section side>
                                <q-item-label class="text-h6 text-bold text-grey">{{ TotalPenalites }} </q-item-label>
                              </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-item clickable v-ripple style="border-right: solid rgb(182, 24, 24) 10px;"> 
                              <q-item-section>
                                Dépenses
                              </q-item-section>
                              <q-item-section side>
                                <q-item-label class="text-h6 text-bold text-grey">{{ TotalDepenses }} </q-item-label>
                              </q-item-section>
                            </q-item>

                            <q-separator />
                            <q-card-actions align="around">
                              <q-item-label class="text-h6 text-bold text-center text-light-blue-10 q-pa-sm">Balance : {{ TotalSolde }} {{ this.vslas.find(option => option.value === this.group).Devise }}</q-item-label>
                            </q-card-actions>
                          </div>
                      </q-card>

                      <div push class="rounded-borders q-pa-xs col-md" id="PrintMe">
                        <q-toolbar class="bg-light-blue-10 text-white shadow-2">
                          <q-toolbar-title v-if="cycleFilter == null">Rapport financier global de l'AVEC</q-toolbar-title>
                          <q-toolbar-title v-if="cycleFilter != null">Rapport financier de l'AVEC au {{ this.cycles.find(option => option.value === this.cycleFilter).label }}</q-toolbar-title>
                          <q-space></q-space>
                          
                          <q-btn rounded outline dense
                            color="orange"
                            icon-right="archive"
                            label="Télécharger"
                            no-caps @click="print"
                        />
                        </q-toolbar>
                        <q-markup-table flat bordered separator="cell" dense>
                            <thead class="bg-grey-3">

                              <tr>
                                <th class="text-right">Opérations</th>
                                <th class="text-right">Entrée (+)</th>
                                <th class="text-right">Sortie (-)</th>
                              </tr>
                            </thead>
                            <tbody class="bg-grey-1">
                              <tr>
                                <td class="text-right">Epargnes encaisées</td>
                                <td class="text-right">{{ this.finrep[0].Epargne }}</td>
                                <td class="text-right">-</td>
                              </tr>
                              <tr>
                                <td class="text-right">Contributions sociales</td>
                                <td class="text-right">{{ this.finrep[0].FS}}</td>
                                <td class="text-right">-</td>
                              </tr>
                              <tr>
                                <td class="text-right">Dons et Legs reçus</td>
                                <td class="text-right">{{ this.finrep[0].Dons }}</td>
                                <td class="text-right">-</td>
                              </tr>
                              <tr>
                                <td class="text-right">Penalités sur membres</td>
                                <td class="text-right">{{ this.finrep[0].Penalites }}</td>
                                <td class="text-right">-</td>
                              </tr>
                              <tr>
                                <td class="text-right">Crédits octroyés</td>
                                <td class="text-right">-</td>
                                <td class="text-right">{{ this.finrep[0].Credit }}</td>
                              </tr>
                              <tr>
                                <td class="text-right">Crédits remboursés</td>
                                <td class="text-right">{{ this.finrep[0].Remboursement }}</td>
                                <td class="text-right">-</td>
                              </tr>
                              <tr>
                                <td class="text-right">Dépenses et Assistances</td>
                                <td class="text-right">-</td>
                                <td class="text-right">{{ this.finrep[0].Depenses }}</td>
                              </tr>
                              <tr>
                                <td class="text-right">Capitaux partagés</td>
                                <td class="text-right">-</td>
                                <td class="text-right">{{ this.finrep[0].Partage }}</td>
                              </tr>
                              <tr class="text-bold">
                                <td class="text-right">Total ({{ this.vslas.find(option => option.value === this.group).Devise }})</td>
                                <td class="text-right">{{ this.finrep[0].Actif }}</td>
                                <td class="text-right">{{ this.finrep[0].Passif }}</td>
                              </tr>
                              
                            </tbody>
                            
                        </q-markup-table>  
                      </div>
                    </div>
                  </q-card-section>

                  <q-separator />
  
                  <q-card-actions>
                      <q-btn flat dense icon="event" :label="new Date().toLocaleDateString()" />
                      <q-space />
                      <q-btn flat dense color="primary" class="text-overline">
                        ® IES MAGOT
                      </q-btn>
                  </q-card-actions>
                </q-card>

            </q-tab-panel>

            <q-tab-panel name="indicators">
              <q-card class="my-card" flat bordered>
                <div class="row">
                  <div v-if="typeInd== 'social'" class="text-h6 text-bold text-light-blue-10 q-pa-sm">INDICATEURS SOCIO-DEMOGRAPHIQUES</div>
                  <div v-if="typeInd== 'meeting'" class="text-h6 text-bold text-light-blue-10 q-pa-sm">INDICATEURS LIES AUX REUNIONS</div>
                  <q-space />
                  <q-option-group align="right" class="q-pa-sm"
                    v-model="typeInd"
                    inline
                    :options="[
                      { label: 'SOCIO-DEMOGRAPHIQUES', value: 'social', color: 'teal' },
                      { label: 'LIES AUX REUNIONS', value: 'meeting', color: 'accent'}
                    ]"
                  />
                </div>
                <q-separator />
                <q-card v-if="group == null">
                  <div class="text-h5 text-bold text-center text-negative q-pa-md"> Please select a group in the list at left to visualize the dashboard</div>
                </q-card>

                <q-tab-panels v-model="typeInd" animated class="shadow-2 rounded-borders">
                  
                    <q-tab-panel name="social" v-if="group != null">
                      <div class="row mt-4"> 
                        <!-- Group composition  -->
                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="250" type="donut" :options="chartOptionsGender" :series="seriesGender"></apexchart>
                        </q-card> 
                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="250" type="donut" :options="chartOptionsInd" :series="seriesInd"></apexchart>
                        </q-card> 
                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="280" type="pie" :options="chartOptionsEducation" :series="seriesEducation"></apexchart>
                        </q-card> 
                      </div>
                      <q-separator></q-separator>
                      <div class="row mt-4"> 
                        <!-- Group Decision making position -->

                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="250" type="donut" :options="chartOptionsGender2" :series="seriesGender2"></apexchart>
                        </q-card> 
                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="280" type="donut" :options="chartOptionsInd2" :series="seriesInd2"></apexchart>
                        </q-card> 
                        <q-card class="rounded-borders q-pa-sm col-md-4">                   
                          <apexchart width="100%" height="280" type="pie" :options="chartOptionsEducation2" :series="seriesEducation2"></apexchart>
                        </q-card> 
                        
                      </div>

                    </q-tab-panel>

                    <q-tab-panel name="meeting" v-if="group != null" class="q-pa-sm">
                      <div class="row mt-4"> 
                        <q-card class="rounded-borders bg-grey-1 q-pa-sm col-md-5">                   
                          <apexchart type="bar" width="100%" height="270"  :options="chartOptionsMeeting" :series="seriesMeeting"></apexchart>
                        </q-card> 
                        <q-card class="rounded-borders q-pa-sm col-md-7">                   
                          <apexchart type="bar" width="100%" height="270"  :options="chartOptionsMeeting1" :series="seriesMeeting1"></apexchart>
                        </q-card>
                      </div>
                      <div class="row mt-4">
                        <q-table class="col-md-8" bordered separator="cell" title="Thèmes discutés en réunions"
                          :data="themes"
                          row-key="name"
                          binary-state-sort
                          :filter="filter" 
                          >
                          <template v-slot:top-right>
                              <q-input filled dense debounce="500" v-model="filter" placeholder="Search Theme" class="col">
                              <template v-slot:append>
                                  <q-icon name="search" />
                              </template>
                              </q-input>
                          </template>

                        </q-table>
                        <q-separator />
                        <q-card class="rounded-borders q-pa-sm col-md-4 bg-grey-1">                   
                          <apexchart type="radialBar" width="100%" height="280" :options="chartOptions" :series="series"></apexchart>
                        </q-card>
                      </div>
                    </q-tab-panel>

                </q-tab-panels>
                <q-separator />
                
            </q-card>
            </q-tab-panel>

            
          </q-tab-panels>
          
        </q-page>
      </q-page-container>
    </q-layout>
  </div>
</template>

<script>
import axios from 'axios'
import Vue from 'vue';
import VueHtmlToPaper from 'vue-html-to-paper';
Vue.use(VueHtmlToPaper);
import VueApexCharts from 'vue-apexcharts';
import mysession from 'vue-session';
Vue.use(mysession, VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  data () {
    return {
      drawer: true,
      tab: 'wallet',
      group: null,
      typeInd: 'social',
      filter: '',
      totalout: null,
      ontime: null,
      overdue: null,
      cycles: [],
      gpbalance: [],
      finrep: [],
      TotalDons: null,
      TotalSocial: null,
      TotalPenalites: null,
      TotalDepenses: null,
      TotalSolde: null,
      cycleFilter: '',
      projects: [],
      projectId: null,
      vslas: [],
      themes: [],
      userId: this.$session.get(1),
      username: this.$session.get(2),
      typeUser: this.$session.get(3),
      NGOId: this.$session.get(11),
      StaffId: this.$session.get(12),
      NGOName: this.$session.get(13),

      selected: [],
      seriesUses: [],
      chartOptionsUses: {
        chart: {
          type: 'pie',
        },
        plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true
                  }
                }
              }
        },
        labels: [],
        colors: ['#911111', '#d1cc64', '#075237', '#063157', '#6a6e05'],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        fill: {
          type: 'gradient',
        },
        legend: {
          formatter: function(val, opts) {
            return val + " : " + opts.w.globals.series[opts.seriesIndex]
          },
          position: 'bottom'
        },
        title: {
          text: "Affectations des crédits",
          position: 'top'
        },
        responsive: [{
          breakpoint: 800,
          options: {
            chart: {
              width: 180
            },
            legend: {
              position: 'top'
            }
          }
        }]
      },

      seriesFunds : [],
      chartOptionsFunds : {

      },

      seriesLoans: [],
      chartOptionsLoans: {},

      seriesRequest: [],
      chartOptionsRequest: {},

      seriesInd: [],
      seriesInd2: [],
      seriesGender: [],
      seriesGender2: [],
      seriesEducation: [],
      seriesEducation2: [],
      seriesMeeting: [],
      seriesMeeting1: [],
      seriesSocial: [],
      seriesSocialrate: [],
      socialcost: null,
      chartOptionsSocial: {

      },
      chartOptionsInd: {
        chart: {
          type: 'pie',
        },
        plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    total: {
                      showAlways: true,
                      show: true
                    }
                  }
                }
              }
        },
        labels: [],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        legend: {
          formatter: function(val, opts) {
            return val + " : " + opts.w.globals.series[opts.seriesIndex]
          }
        },
        title: {
          text: 'Composition du groupe par âge',
          position: 'top'
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'top'
            }
          }
        }]
      },

      chartOptionsInd2: {
        chart: {
          type: 'donut',
        },
        plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    total: {
                      showAlways: true,
                      show: true
                    }
                  }
                }
              }
        },
        labels: [],
        colors: ['#2f5378', '#5e1b05', '#075237', '#063157', '#6a6e05'],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        fill: {
          type: 'gradient',
        },
        legend: {
          formatter: function(val, opts) {
            return val + " : " + opts.w.globals.series[opts.seriesIndex]
          }
        },
        title: {
          text: 'Age et prise des décisions',
          position: 'top'
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'top'
            }
          }
        }]
      },

      chartOptionsGender: {
            chart: {
              type: 'donut',
            },
            plotOptions: {
              pie: {
                startAngle: -90,
                endAngle: 90,
                offsetY: 10,
                donut: {
                  labels: {
                    show: true,
                    total: {
                      showAlways: true,
                      show: true
                    }
                  }
                }
              }
            },
            labels: [],
            colors: ['#2d4d1f', '#752975cc'],
            title: {
              text: 'Composition du groupe par genre',
              position: 'center'
            },
            legend: {
              position: 'bottom'
            },
            grid: {
              padding: {
                bottom: -80
              }
            },
            responsive: [{
              breakpoint: 480,
              options: {
                chart: {
                  width: 200
                },
                legend: {
                  position: 'bottom'
                }
              }
            }]
      },

      chartOptionsGender2: {
            chart: {
              type: 'donut',
            },
            plotOptions: {
              pie: {
                startAngle: -90,
                endAngle: 90,
                offsetY: 10,
                donut: {
                  labels: {
                    show: true,
                    total: {
                      showAlways: true,
                      show: true
                    }
                  }
                }
              }
            },
            labels: [],
            colors: ['#d93b07', '#0a2b52'],
            title: {
              text: 'Genre et prise de déciions',
              position: 'center'
            },
            legend: {
              position: 'bottom'
            },
            grid: {
              padding: {
                bottom: -80
              }
            },
            responsive: [{
              breakpoint: 480,
              options: {
                chart: {
                  width: 200
                },
                legend: {
                  position: 'bottom'
                }
              }
            }]
      },
      chartOptionsEducation: {
        chart: {
          type: 'pie',
        },
        plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                  }
                }
              }
        },
        labels: [],
        colors: ['#e6a729', '#544e54e0', '#075237', '#063157', '#6a6e05'],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        legend: {
          formatter: function(val, opts) {
            return val + " : " + opts.w.globals.series[opts.seriesIndex]
          }
        },
        title: {
          text: "Composition du groupe par niveau d'étude",
          position: 'top'
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'top'
            }
          }
        }]
      },
      chartOptionsEducation2: {
        chart: {
          type: 'pie',
        },
        plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true
                  }
                }
              }
        },
        labels: [],
        colors: ['#911111', '#d1cc64', '#075237', '#063157', '#6a6e05'],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        fill: {
          type: 'gradient',
        },
        legend: {
          formatter: function(val, opts) {
            return val + " : " + opts.w.globals.series[opts.seriesIndex]
          },
          position: 'right'
        },
        title: {
          text: "Niveau d'étude et prise des décisions",
        },
        responsive: [{
          breakpoint: 500,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      },
      chartOptionsMeeting : {

      },
      chartOptionsMeeting1 : {

      },
      series: [],
      chartOptions: {
        chart: {
          height: 350,
          type: 'radialBar',
          toolbar: {
            show: true
          }
        },
        plotOptions: {
          radialBar: {
            startAngle: -135,
            endAngle: 225,
              hollow: {
              margin: 0,
              size: '70%',
              background: '#fff',
              image: undefined,
              imageOffsetX: 0,
              imageOffsetY: 0,
              position: 'front',
              dropShadow: {
                enabled: true,
                top: 3,
                left: 0,
                blur: 4,
                opacity: 0.24
              }
            },
            track: {
              background: '#fff',
              strokeWidth: '67%',
              margin: 0, // margin is in pixels
              dropShadow: {
                enabled: true,
                top: -3,
                left: 0,
                blur: 4,
                opacity: 0.35
              }
            },
        
            dataLabels: {
              show: true,
              name: {
                offsetY: -10,
                show: true,
                color: '#888',
                fontSize: '17px'
              },
              value: {
                formatter: function(val) {
                  return parseInt(val);
                },
                color: '#111',
                fontSize: '36px',
                show: true,
              }
            }
          }
        },
        title: {
          text: 'Taux de participation aux réunions',
          position: 'center'
        },
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            type: 'horizontal',
            shadeIntensity: 0.5,
            gradientToColors: ['#ABE5A1'],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 100]
          }
        },
        stroke: {
          lineCap: 'round'
        },
        labels: ['%'],
      },

      rateloan: 63,
      step: 1,
      chartOptions1: {
        chart: {
          height: 350,
          type: 'radialBar',
          toolbar: {
            show: true
          }
        },
        plotOptions: {
          radialBar: {
            startAngle: -135,
            endAngle: 225,
              hollow: {
              margin: 0,
              size: '70%',
              background: '#fff',
              image: undefined,
              imageOffsetX: 0,
              imageOffsetY: 0,
              position: 'front',
              dropShadow: {
                enabled: true,
                top: 3,
                left: 0,
                blur: 4,
                opacity: 0.24
              }
            },
            track: {
              background: '#fff',
              strokeWidth: '67%',
              margin: 0, // margin is in pixels
              dropShadow: {
                enabled: true,
                top: -3,
                left: 0,
                blur: 4,
                opacity: 0.35
              }
            },
        
            dataLabels: {
              show: true,
              name: {
                offsetY: -10,
                show: true,
                color: '#888',
                fontSize: '17px'
              },
              value: {
                formatter: function(val) {
                  return parseInt(val);
                },
                color: '#111',
                fontSize: '36px',
                show: true,
              }
            }
          }
        },
        title: {
          text: "Taux d'assistance sociale",
          position: 'center'
        },
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            type: 'horizontal',
            shadeIntensity: 0.5,
            gradientToColors: ['#ba1616'],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 100]
          }
        },
        stroke: {
          lineCap: 'round'
        },
        labels: ['%'],
      },
    }
  },
  created () {
    this.listvslas()
    this.listProject()
  },
  methods: {
    listProject () {
      axios.get(this.$urlServer + 'projectControllers/listProject.php', {
      params: {
          ngoId: this.NGOId
      }
      }).then((dataFront) => {
      this.projects = dataFront.data
      })
    },
    listvslas () {
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
    listprojectvslas () {
      this.group = null
      axios.get(this.$urlServer + 'vslaControllers/listVSLAPerProject.php', {
          params: {
            projectId: this.projectId
          }
      }).then((dataFront) => {
          this.vslas = dataFront.data
      })
    },
    listCycles () {
      axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
          this.cycles = dataFront.data
          this.cycleFilter = null
      })
    },
    listGpBalance () {
      axios.get(this.$urlServer + 'shareout/groupBalance.php', {
        params: {
          cycleId: this.cycleFilter
        }
      }).then((dataFront) => {
          this.gpbalance = dataFront.data
          this.TotalDons = dataFront.data[0].Dons
          this.TotalSocial = dataFront.data[0].Socials
          this.TotalPenalites = dataFront.data[0].Penalites
          this.TotalDepenses = dataFront.data[0].Depenses
          this.TotalSolde = dataFront.data[0].Solde
      })
    },
    allGpBalance () {
      axios.get(this.$urlServer + 'shareout/groupBalancePerVSLA.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
          this.gpbalance = dataFront.data
          this.TotalDons = dataFront.data[0].Dons
          this.TotalSocial = dataFront.data[0].Socials
          this.TotalPenalites = dataFront.data[0].Penalites
          this.TotalDepenses = dataFront.data[0].Depenses
          this.TotalSolde = dataFront.data[0].Solde
      })
    },
    getReport () {
      axios.get(this.$urlServer + 'dashboardControllers/rapportFinancierDash.php', {
          params: {
            cycleId: this.cycleFilter
          }
      }).then((dataFront) => {
        this.finrep = dataFront.data
      }) 
    },
  
    globalreport () {
      axios.get(this.$urlServer + 'dashboardControllers/rapportFinancierAllDash.php', {
          params: {
            vslaId: this.group
          }
      }).then((dataFront) => {
        this.finrep = dataFront.data
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
    savingloan () {
      axios.get(this.$urlServer + 'dashboardControllers/loarnSavingsDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsLoans = dataFront.data[0].chartOptions
        this.seriesLoans = dataFront.data[0].series
        
      })
    },
    genderComposition () {
      axios.get(this.$urlServer + 'dashboardControllers/genderDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsGender.labels = dataFront.data[0].entete
        this.seriesGender = dataFront.data[0].donnees
        
      })
    },
    genderPosition () {
      axios.get(this.$urlServer + 'dashboardControllers/genderPosistionDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsGender2.labels = dataFront.data[0].entete
        this.seriesGender2 = dataFront.data[0].donnees
        
      })
    },
    ageComposition () {
      axios.get(this.$urlServer + 'dashboardControllers/ageCompositionDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsInd.labels = dataFront.data[0].entete
        this.seriesInd = dataFront.data[0].donnees
        
      })
    },
    agePosition () {
      axios.get(this.$urlServer + 'dashboardControllers/agePositionDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsInd2.labels = dataFront.data[0].entete
        this.seriesInd2 = dataFront.data[0].donnees
        
      })
    },
    educationComposition () {
      axios.get(this.$urlServer + 'dashboardControllers/educationDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsEducation.labels = dataFront.data[0].entete
        this.seriesEducation = dataFront.data[0].donnees
        
      })
    },
    educationPosition () {
      axios.get(this.$urlServer + 'dashboardControllers/educationPositionDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsEducation2.labels = dataFront.data[0].entete
        this.seriesEducation2 = dataFront.data[0].donnees
        
      })
    },
    numberMeeting () {
      axios.get(this.$urlServer + 'dashboardControllers/meetingDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsMeeting = dataFront.data[0].chartOptions
        this.seriesMeeting = dataFront.data[0].series
        
      })
    },
    meetingParticipation () {
      axios.get(this.$urlServer + 'dashboardControllers/participantDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsMeeting1 = dataFront.data[0].chartOptions
        this.seriesMeeting1 = dataFront.data[0].series
        
      })
    },
    attendacyParticipation () {
      axios.get(this.$urlServer + 'dashboardControllers/pourcentageDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.series = dataFront.data[0].donnees 
      })
    },
    themeDiscussion () {
      axios.get(this.$urlServer + 'dashboardControllers/frequencyMeetingDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.themes = dataFront.data
        
      })
    },
    socialcaseyear () {
      axios.get(this.$urlServer + 'dashboardControllers/assistanceFrequency.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsSocial = dataFront.data[0].chartOptions
        this.seriesSocial = dataFront.data[0].series
        
      })
    },

    socialcaserate () {
      axios.get(this.$urlServer + 'dashboardControllers/assistanceFrequencyDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.seriesSocialrate = dataFront.data[0].donnees
        this.socialcost = dataFront.data[0].entete
      })
    },

    requestLoan () {
      axios.get(this.$urlServer + 'dashboardControllers/creditRequestLanDash.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsRequest = dataFront.data[0].chartOptions
        this.seriesRequest = dataFront.data[0].series
        
      })
    },

    useLoan () {
      axios.get(this.$urlServer + 'dashboardControllers/useLoansDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsUses.labels = dataFront.data[0].entete
        this.seriesUses = dataFront.data[0].donnees
        
      })
    },

    socialFundsUse () {
      axios.get(this.$urlServer + 'dashboardControllers/socialFundsDashboard.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.chartOptionsFunds = dataFront.data[0].chartOptions
        this.seriesFunds = dataFront.data[0].series
        
      })
    },
    outstandingLoan () {
      axios.get(this.$urlServer + 'dashboardControllers/OutstandingLoanDash.php', {
        params: {
          cycleId: this.cycleFilter
        }
      }).then((dataFront) => {
        this.totalout = dataFront.data[0].totalLoan
        this.overdue = dataFront.data[0].overdue
        this.ontime = dataFront.data[0].ontime
        
      })
    },
    outLoansAll () {
      axios.get(this.$urlServer + 'dashboardControllers/OutstandingLoanDashPerVSLA.php', {
        params: {
          vslaId: this.group
        }
      }).then((dataFront) => {
        this.totalout = dataFront.data[0].totalLoan
        this.overdue = dataFront.data[0].overdue
        this.ontime = dataFront.data[0].ontime
        
      })
    },

    dashCycle () {
      this.listGpBalance()
      this.outstandingLoan()
      this.getReport()
    },
    showDashboard() {
      this.listCycles()
      this.socialFundsUse ()
      this.useLoan ()
      this.savingloan ()
      this.requestLoan()
      this.genderComposition ()
      this.ageComposition ()
      this.educationComposition ()
      this.genderPosition ()
      this.agePosition ()
      this.educationPosition ()
      this.numberMeeting ()
      this.meetingParticipation () 
      this.themeDiscussion()
      this.attendacyParticipation()
      this.socialcaseyear()
      this.socialcaserate()
      this.globalreport()
      this.allGpBalance()
      this.outLoansAll()
    }

  },

  components: {
  }
}
</script>
