<template>
    <q-page class="q-pa-xs">
      <q-card>
        <q-card-section class="bg-grey-1 text-white">
          <div class="text-h6 text-light-blue-10 text-center">RAPPORTS DES REUNIONS</div>
        </q-card-section>
        <q-separator></q-separator>
        <q-tabs v-model="tab"
            dense
            class="text-grey"
            active-color="primary"
            indicator-color="primary"
            align="justify"
            narrow-indicator shrink>
          <q-tab no-caps dense name="list" label="Rapports précédents" icon="summarize" class="text-primary"/>
          <q-tab no-caps dense name="add" label="Ajouter un rapport" icon="library_add" class="text-accent" />
          
        </q-tabs>
        <q-separator />
        
        <q-tab-panels v-model="tab">
          <q-tab-panel name="list">
            <q-card flat>
              <q-card-section class="bg-grey-1">
                <div class="row flex flex-center">
                  <q-item-label class="text-h6 q-pa-sm text-light-blue-10">Filtrer par</q-item-label>
                  <div class="col q-pa-xs" align="left">
                    <q-select @input="listmeetings" class="col" dense outlined :options="cycles" v-model="cycleFilter" ref="cycleFilter" label="Cycle" map-options emit-value    
                      >
                    </q-select>
                  </div>
                  
                </div>
              </q-card-section>
            <q-separator></q-separator>
  
            <q-table bordered flat separator="cell" class="col q-pa-sm"
                :data="meetings"
                :columns="columns"
                row-key="name"
                binary-state-sort
                :filter="filter" 
                grid
                hide-header
                selection="single"
                :selected.sync="selected"
                >
  
                <template v-slot:item="props">
                  <div
                    class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
                    :style="props.selected ? 'transform: scale(0.95);' : ''"
                  >
                    <q-card :class="props.selected ? 'bg-grey-2' : ''">
                      <q-card-section class="text-subtitle2">
                        <q-checkbox dense v-model="props.selected" :label="props.row.label" />
                      </q-card-section>
                      <q-separator />
                      <q-list dense>
                        <q-item v-for="col in props.cols.filter(col => col.name !== 'desc')" :key="col.name">
                          <q-item-section>
                            <q-item-label>{{ col.label }}</q-item-label>
                          </q-item-section>
                          <q-item-section side>
                            <q-item-label caption>{{ col.value }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <q-separator></q-separator>
                      <q-card-actions align="around">
                        <q-btn side flat no-caps dense icon="edit" color="primary" label="Modifier"></q-btn>
                        <q-btn side flat no-caps dense icon="delete" color="negative" label="Supprimer"></q-btn>
                      </q-card-actions>
                    </q-card>
                  </div>
                </template>
  
                <template v-slot:top-right>
                    <q-input dense outlined debounce="500" v-model="filter" placeholder="Rechercher un rapport" class="col">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                    </q-input>
                </template>
  
            </q-table>
          </q-card>
          </q-tab-panel>
          <q-tab-panel name="add">
            <q-card flat>
              <q-card-section class="bg-accent">
                <q-item-label class="text-subtitle2 text-bold text-white">Nouveau rapport de la réunion</q-item-label>
              </q-card-section>
              <q-separator></q-separator>
              <q-form class="full-width q-pa-sm">
                <div class="row q-mb-sm">
                    <q-input dense outlined aria-required="required" ref="date" mask="####-##-##" v-model="date" label="Date de la réunion" :rules="[ myRule ]" class="col">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy">
                            <q-date v-model="date" mask="YYYY-MM-DD"></q-date>
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                </div>
                
                <div class="row q-mb-sm">
                    <q-select dense outlined :options="topics" :rules="[myRule]"  v-model="topic" ref="topic" label="Thème principal discuté" class="col" emit-value map-options   
                    >
                    </q-select>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" dense outlined v-model="participant" ref="participant" label="Nombre total de participants" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" dense outlined v-model="men" ref="men" label="Nombre d'hommes présents" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" dense outlined v-model="women" ref="women" label="Nombre de femmes présentes" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                <div class="row q-mb-sm">
                    <q-input type="number" dense outlined v-model="socialcase" ref="socialcase" label="Nombre de cas sociaux rapportés" :rules="[ myRule ]" class="col">
                    </q-input>
                </div>
                  
              <div align="right">
                  <q-btn dense
                      @click="addMeeting"
                      label="Enregistrer"
                      color="accent"
                      icon="save"
                      outline
                      type="submit">
                  </q-btn>
              </div>
              </q-form>
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </q-page>
  </template>
  
  <script>
  import axios from 'axios'
  
  const columns = [
        { name: 'value', align: 'right', label: 'ID', field: 'value', sortable: true },
        { name: 'Date', align: 'center', label: 'Date de la réunion', field: 'Date', sortable: true },
        { name: 'Participant',  align: 'right', label: 'Nombre des participants', field: 'Participant', sortable: true },
        { name: 'Men', align: 'left', label: "Nombre d'hommes", field: 'Men', sortable: true  },
        { name: 'Women', align: 'left', label: 'Nombre de femmes', field: 'Women', sortable: true  },
        { name: 'Socialcase', align: 'left', label: 'Nombre de cas sociaux rapportés', field: 'Socialcase', sortable: true  },
    ]
  export default {
    data () {
      const addMeeting = () => {
      var form = new FormData()
      form.append('date', this.date)
      form.append('topic', this.topic)
      form.append('participant', this.participant)
      form.append('men', this.men)
      form.append('women', this.women)
      form.append('socialcase', this.socialcase)
      form.append('vslaId', this.vslaId)
      if (this.date !== null && this.topic !== null && this.participant !== null && this.men !== null && this.women !== null && this.socialcase !== null) {
        if ((this.participant - this.women) == this.men) {
            axios.post(this.$urlServer + 'meetingControllers/addMeeting.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Le compte rendu de la réunion a bien été enregistré'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listmeetings()
            })
          })
            
        } else {
            this.$q.dialog({
              title: 'Erreur',
              message: ("Le nombre de femmes et d'hommes ne correspond pas au nombre total des participants"),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              this.listmeetings()
            })
        }
      } else {
          this.$q.dialog({
            title: 'Erreur',
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
        addMeeting,
        columns,
        tab: 'list',
        date: null,
        topic: null,
        participant: null,
        men: null,
        women: null,
        socialcase: null,
        filter: '',
        selected: [],
        cycleFilter: null,
        cycles: [],
        topics: [
            {label: 'Entrepreneuriat', value: 'Entrepreneurship'},
            {label: 'Education financière', value: 'Financial education'},
            {label: 'Cohésion Sociale', value: 'Social Cohesion'},
            {label: 'Leadership féminin', value: 'Female leadership'},
            {label: 'Autonomisation de la femme', value: "Women's empowerment"},
            {label: 'Autre thématique', value: 'Other theme'},
        ],
        meetings: [],
        userId: this.$session.get(1),
        username: this.$session.get(2),
        typeUser: this.$session.get(3),
        userphone: this.$session.get(5),
        vslaId: this.$session.get(6),
        vslaName: this.$session.get(7),
        memberId: this.$session.get(8)
      }
    },
    created () {
        this.listCycles()
    },
    methods: {
        myRule (val) {
            return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(!!val || '* Required')
            }, 800)
            })
        },
        listmeetings () {
            axios.get(this.$urlServer + 'meetingControllers/listMeeting.php', {
                params: {
                  cycleId: this.cycleFilter
                }
            }).then((dataFront) => {
                this.meetings = dataFront.data
            })
        },
        listCycles () {
            axios.get(this.$urlServer + 'cycleControllers/listAllCycle.php', {
                params: {
                    vslaId: this.vslaId
                }
            }).then((dataFront) => {
                this.cycles = dataFront.data
            })
        },
    }
  }
  </script>
  
  <style lang="sass">
  .grid-style-transition
    transition: transform .28s, background-color .28s
  </style>
    