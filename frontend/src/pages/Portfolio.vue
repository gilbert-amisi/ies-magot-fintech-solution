<template>
    <div class="q-pa-md">
      <q-layout view="hHh Lpr lff" container style="height: 850px" class="shadow-2 rounded-borders">
        <q-header elevated class="bg-grey-3">
          <q-toolbar>
            <q-btn flat @click="drawer = !drawer" round dense icon="dashboard" color="negative" />
            <q-toolbar-title v-if="group != null" class="text-h6 text-light-blue-10 text-bold" style="font-family: Helvetica">VUE DU PROJET {{ this.projects.find(option => option.value === this.group).label }}</q-toolbar-title>
            <q-toolbar-title v-if="group == null" class="text-h6 text-light-blue-10 text-bold" style="font-family: Helvetica">VUE DU PORTEFEUILLE {{ NGOName.toUpperCase() }}</q-toolbar-title>
            <q-tabs shrink inline-label>          
              <q-btn to="/vsladash" stretch label="SUIVI DES AVEC" icon="dashboard" color="light-blue-10"/>
            </q-tabs>
            
          </q-toolbar>
        </q-header>
  
        <q-footer elevated style="background-color: #2B2D3E;">
          <q-toolbar>
            <q-toolbar-title header class="text-h6 text-center text-grey-1" style="font-family: Helvetica">Powered by IES & DML</q-toolbar-title>
          </q-toolbar>
        </q-footer>
  
        <q-drawer
          v-model="drawer"
          :width="250"
          :breakpoint="500"
          show-if-above
          bordered
          content-class="text-white"
          style="font-size: 16px; background-color: #2B2D3E;"
        >
          <q-scroll-area class="fit" style="background-color: #2B2D3E;">
            <q-list separator>
                <q-item-label header class="text-subtitle2 text-bold text-white">FILTRER PAR PROJET</q-item-label> <q-separator dark/>
                <q-option-group
                  :options="projects"
                  type="radio"
                  name="group"
                  ref="group"
                  color="grey-1"
                  keep-color
                  v-model="group"
                  value=""
                  @input="showbyProject"
                  />
  
              <q-separator dark/>
  
            </q-list>
          </q-scroll-area>
        </q-drawer>
  
        <q-page-container>
          <q-page style="background-color: #343E59;">
            <q-card bordered class="q-pa-sm" style="background-color: #343E59;">
                <q-card-section>
                    <div class="row mt-4 q-gutter-xs">
                        <q-card class="rounded-borders text-white q-pa-sm flex col-md" style="background: radial-gradient(circle, #7e98ad 0%, rgb(197, 157, 83) 100%)">
                          <div class="text-h6">{{ nbgroup }}</div>
                          <q-space></q-space>
                          <div><q-icon name="bubble_chart" color="white" size="20px"></q-icon></div>  
                          <q-linear-progress size="7px" :value="1" color="grey-5"></q-linear-progress>
                          <div class="text-overline">AVEC créées</div>
                          <q-space></q-space>
                          <div><q-icon name="bubble_chart" color="white" size="20px"></q-icon></div> 
                        </q-card>

                        <div class="rounded-borders text-white q-pa-sm flex col-md" style="background: radial-gradient(circle, #35a2ff 0%, #014a88 100%)">
                          <div class="text-h6">{{ nbpeople }} </div>
                          <q-space></q-space>
                          <div><q-spinner-dots size="20px" color="white" /></div>
                          <q-linear-progress size="7px" :value="1" color="grey-5"></q-linear-progress>
                          <div class="text-overline">Bénéficiaires</div>
                          <q-space></q-space>
                          <div><div><q-icon name="diversity_3" color="white" size="20px"></q-icon></div> </div>
                        </div>

                        <div class="rounded-borders text-white q-pa-sm col-md flex" style="background: radial-gradient(circle, #c5d8e8 0%, #6b2323 100%)">
                            <div class="text-h6">{{ nbwomen }}</div>
                            <q-space></q-space>
                            <div><q-spinner-rings color="grey-3" size="20px" /></div>
                            <q-linear-progress size="7px" :value="perwomen/100" color="grey-5">
                            </q-linear-progress>
                            <div class="text-overline">Femmes et filles</div>
                          <q-space></q-space>
                          <div class="text-h6">{{ perwomen }}%</div>                        
                        </div>

                        <div class="rounded-borders text-white q-pa-sm flex col-md" style="background: radial-gradient(circle, #c5d8e8 0%, #278265 100%)">
                          <div class="text-h6 text-bold">{{ nbmarginal }}</div>
                          <q-space></q-space>
                          <q-item-label class="text-overline"> membres issus des</q-item-label>
                          <q-space></q-space>
                          <div>
                            <q-icon color="grey-3" size="20px" name="diversity_1" />
                          </div>
                          <q-linear-progress size="7px" :value="permarginal/100" color="grey-5">
                        </q-linear-progress>
                          <div class="text-overline">Groupes marginalisés</div>
                          <q-space></q-space>
                          <div class="text-h6">{{ permarginal }}%</div>
                        </div>

                    </div>
                </q-card-section>

                <q-separator></q-separator>

                <q-card-section class="row mt-4 q-gutter-xs">
                  <q-card class="rounded-borders q-pa-xs col-md-8" separator style="height: 300px; background-color: #2B2D3E;">
                    <q-item class="full-width">
                      <apexchart class="full-width" width="100%" height="280" type="bar" :options="chartOptionsSavings" :series="seriesSavings"></apexchart>
                    </q-item>
                  </q-card>
                  <q-card class="rounded-borders text-white q-pa-xs col-md" style="height: 300px; background-color: #2B2D3E;">
                      <q-item class="full-width" v-if="optionsvsla.labels.length > 0">
                        <apexchart width="100%" height="280" type="pie" :options="optionsvsla" :series="seriesvsla"></apexchart>   
                      </q-item>
                    </q-card>
                </q-card-section>

                <q-separator></q-separator>

                <q-card-section class="row mt-4 q-gutter-xs" >
                  <q-card class="rounded-borders text-white q-pa-xs col-md" style="height: 300px; background-color: #2B2D3E;">
                    <q-item class="full-width" v-if="optionsben.labels.length > 0">
                      <apexchart width="100%" height="280" type="pie" :options="optionsben" :series="seriesben"></apexchart>   
                    </q-item>
                  </q-card>
                  
                  <q-card class="rounded-borders q-pa-xs col-md-8" separator style="height: 300px; background-color: #2B2D3E;">
                    <q-item class="full-width">
                      <apexchart class="full-width" width="100%" height="280" type="bar" :options="chartOptionsLoans" :series="seriesLoans"></apexchart>
                    </q-item>
                  </q-card>
                  
                </q-card-section>

            </q-card>
            
          </q-page>
        </q-page-container>
      </q-layout>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  // import { exportFile } from 'quasar'
  import Vue from 'vue'
  import VueApexCharts from 'vue-apexcharts'
  import mysession from 'vue-session'
  Vue.use(mysession, VueApexCharts)
  Vue.component('apexchart', VueApexCharts)
  
  export default {
    data () {
      return {
        drawer: true,
        group: null,
        filter: '',
        projects: [],
        nbgroup: null,
        nbpeople: null,
        nbwomen: null,
        perwomen: null,
        nbmarginal: null,
        permarginal: null,

        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        NGOId: this.$session.get(11),
        StaffId: this.$session.get(12),
        NGOName: this.$session.get(13),
  
        selected: [],

        seriesSavings: [],
        chartOptionsSavings: {},

        seriesLoans: [],
        chartOptionsLoans: {},


        seriesvsla: [],
        optionsvsla: {
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
            // formatter: function(val, opts) {
            //   return val + " : " + opts.w.globals.series[opts.seriesIndex]
            // },
            position: 'bottom',
            labels: {
              colors: '#fff', // Set custom legend text color (e.g., dark gray)
            },
          },
          title: {
            text: "Nombre d'AVEC par province",
            position: 'top',
            style: {
              fontSize: '16px', // Font size
              fontWeight: 'bold', // Font weight
              fontFamily: 'Arial, sans-serif', // Font family
              color: '#FFF', // Custom title color (e.g., orange)
            },
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

        seriesben: [],
        optionsben: {
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
          colors: ['#c408c4', '#1906c7cc', '#F55555', '#063157', '#6a6e05'],
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
            // formatter: function(val, opts) {
            //   return val + " : " + opts.w.globals.series[opts.seriesIndex]
            // },
            position: 'bottom',
            labels: {
              colors: '#fff', // Set custom legend text color (e.g., dark gray)
            },
          },
          title: {
            text: "Nombre de bénéficiaires par province",
            position: 'top',
            style: {
              fontSize: '16px', // Font size
              fontWeight: 'bold', // Font weight
              fontFamily: 'Arial, sans-serif', // Font family
              color: '#FFF', // Custom title color (e.g., orange)
            },
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

      }
    },
    created () {
      this.listProject()
      this.loanperngo()
      this.savingperngo()
      this.globalvsprov()
      this.globalbenprov()
      this.globalCard()
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
        globalCard () {
          axios.get(this.$urlServer + 'allDashboardControllers/memberDash.php', {
          params: {
            ngoId: this.NGOId
          }
          }).then((dataFront) => {
              this.nbgroup = dataFront.data[0].VSLA
              this.nbpeople = dataFront.data[0].Beneficiaries
              this.nbwomen = dataFront.data[0].Women
              this.perwomen = dataFront.data[0].PerWomen
              this.nbmarginal = dataFront.data[0].Marginalized
              this.permarginal = dataFront.data[0].PerMarginalised
          })
        },
        projectCard () {
          axios.get(this.$urlServer + 'allDashboardControllers/memberPerProjectDash.php', {
          params: {
            projectId: this.group
          }
          }).then((dataFront) => {
              this.nbgroup = dataFront.data[0].VSLA
              this.nbpeople = dataFront.data[0].Beneficiaries
              this.nbwomen = dataFront.data[0].Women
              this.perwomen = dataFront.data[0].PerWomen
              this.nbmarginal = dataFront.data[0].Marginalized
              this.permarginal = dataFront.data[0].PerMarginalised
          })
        },
        loanperngo () {
          axios.get(this.$urlServer + 'allDashboardControllers/loanPerYearDash.php', {
          params: {
            ngoId: this.NGOId
          }
          }).then((dataFront) => {
          this.chartOptionsLoans = dataFront.data[0].chartOptions
          this.seriesLoans = dataFront.data[0].series
          
          })
        },
        loanperproject () {
          axios.get(this.$urlServer + 'allDashboardControllers/loanPerProjectDash.php', {
          params: {
            projectId: this.group
          }
          }).then((dataFront) => {
          this.chartOptionsLoans = dataFront.data[0].chartOptions
          this.seriesLoans = dataFront.data[0].series
          
          })
        },

        savingperngo () {
          axios.get(this.$urlServer + 'allDashboardControllers/survingPerYear.php', {
          params: {
            ngoId: this.NGOId
          }
          }).then((dataFront) => {
          this.chartOptionsSavings = dataFront.data[0].chartOptions
          this.seriesSavings= dataFront.data[0].series
          
          })
        },

        savingperproject () {
          axios.get(this.$urlServer + 'allDashboardControllers/survingPerYearPerProject.php', {
          params: {
            projectId: this.group
          }
          }).then((dataFront) => {
          this.chartOptionsSavings = dataFront.data[0].chartOptions
          this.seriesSavings= dataFront.data[0].series
          
          })
        },
        
        globalvsprov () {
          axios.get(this.$urlServer + 'allDashboardControllers/vslaPerProvinceDash.php', {
          params: {
            ngoId: this.NGOId
          }
          }).then((dataFront) => {
              this.optionsvsla.labels = dataFront.data[0].entete
              this.seriesvsla = dataFront.data[0].donnees
          })
          
        },
        projectvsprov () {
          axios.get(this.$urlServer + 'allDashboardControllers/vslaPerProvincePerProjectDash.php', {
          params: {
            projectId: this.group
          }
          }).then((dataFront) => {
              this.optionsvsla.labels = dataFront.data[0].entete
              this.seriesvsla = dataFront.data[0].donnees
          })
          
        },
        globalbenprov () {
          axios.get(this.$urlServer + 'allDashboardControllers/memberPerProvinceDash.php', {
          params: {
            ngoId: this.NGOId
          }
          }).then((dataFront) => {
              this.seriesben = dataFront.data[0].donnees
              this.optionsben.labels = dataFront.data[0].entete
          })
          
        },

        projectbenprov () {
          axios.get(this.$urlServer + 'allDashboardControllers/memberProvincePerProjectDash.php', {
          params: {
            projectId: this.group
          }
          }).then((dataFront) => {
              this.seriesben = dataFront.data[0].donnees
              this.optionsben.labels = dataFront.data[0].entete
          })
          
        },
        showbyProject () {
          this.projectCard()
          this.loanperproject()
          this.savingperproject()
          this.projectvsprov()
          this.projectbenprov()
        }
    },
  
    components: {
    }
  }
  </script>
  