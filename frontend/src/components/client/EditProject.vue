<template>
    <q-card flat style="width: 100%; max-width: 600px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6">PROJECT {{ titleP }} UPDATING</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
            <div class="row q-mb-sm">
                <q-input dense outlined v-model="title" ref="title" label="Title" :rules="[ myRule ]" class="col">
                
                </q-input>
            </div>
        
            <div class="row q-mb-sm">
            <q-input dense outlined aria-required="required" ref="start" mask="####-##-##" v-model="start" label="Start date" :rules="[ myRule ]" class="col">
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
            <q-input dense outlined aria-required="required" ref="end" mask="####-##-##" v-model="end" label="End date" :rules="[ myRule ]" class="col">
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
            <q-select dense outlined v-model="staffId" ref="staffId" label="Project Manager" class="col" 
                :rules="[ myRule ]"
                :options="staffs" 
                map-options emit-value>
            </q-select>
            </div>
            <div align="right">
            <q-btn
            @click="editProject"
            label="Save changes"
            color="primary"
            icon="save"
            outline
            type="submit">
            </q-btn>
            </div>
        </q-form>
      </q-card-section>
    </q-card>
  </template>

  <script>
    import Vue from 'vue'
    import axios from 'axios'
    import mysession from 'vue-session'
    Vue.use(mysession)
  export default {
    props: ['idProject', 'titleP', 'startP', 'endP', 'staffP'],
    data () {
        const editProject = () => {
        var form = new FormData()
        form.append('projectId', this.idProject)
        form.append('title', this.title)
        form.append('start', this.start)
        form.append('end', this.end)
        form.append('staffId', this.staffId)
        if (this.title !== null && this.start !== null && this.end !== null && this.staffId !== null) {
            axios.post(this.$urlServer + 'projectControllers/editProject.php', form, {
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
        editProject,
          title: this.titleP,
          staffId: this.staffP,
          start: this.startP,
          end: this.endP,
          status: 'In progress',
          staffs: [],
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
            axios.get(this.$urlServer + 'staffControllers/listStaffPM.php', {
            params: {
                ngoId: this.NGOId
            }
            }).then((dataFront) => {
            this.staffs = dataFront.data
            })
        },
    }
  }
  </script>
  