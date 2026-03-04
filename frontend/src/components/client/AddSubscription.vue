<template>
    <q-card flat style="width: 100%; max-width: 800px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6">New subscription for {{ nameClient }}</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
            <div class="row q-mb-sm">
                <q-select :options="levels" outlined :rules="[myRule]" use-chips @filter="filterFn"
                    v-model="level" ref="level" label="Category" class="col" emit-value  
                >
                </q-select>
            </div>
            <div class="row q-mb-sm">
                <q-input outlined :rules="[myRule]" v-model="maxnumber" type="number"
                    ref="maxnumber" label="Max number of users" class="col"
                >
                </q-input>
            </div>
            <div class="row q-mb-sm">
                <q-input outlined aria-required="required" ref="start" mask="####-##-##" v-model="start" label="Start date" :rules="[ myRule ]" class="col">
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
                <q-input outlined aria-required="required" ref="end" mask="####-##-##" v-model="end" label="End date" :rules="[ myRule ]" class="col">
                <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy">
                        <q-date v-model="end" mask="YYYY-MM-DD"></q-date>
                    </q-popup-proxy>
                    </q-icon>
                </template>
                </q-input>
            </div>
            <div align="right">
                <q-btn
                    @click="addSubscription"
                    label="Save client"
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
  import axios from 'axios'
  const stringOptions = ['Basic', 'Medium', 'Unlimited']

  export default {
    props: ['idClient', 'nameClient'],
    data () {
      const addSubscription = () => {
      var form = new FormData()
      form.append('clientId', this.idClient)
      form.append('maxnumber', this.maxnumber)
      form.append('start', this.start)
      form.append('end', this.end)
      form.append('level', this.level)
      if (this.$maxnumber !== null && this.start !== null && this.end !== null && this.start <= this.end  && this.level !== null) {
          axios.post(this.$urlServer + 'subscriptionControllers/addSubscription.php', form, {
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: ('Client subscription successfully saved'),
              ok: {
                push: true
              },
              cancel: {
                color: 'negative'
              },
              persistent: true
            }).onOk(() => {
              window.location.reload()
            })
          })
        } else {
          this.$q.dialog({
            website: 'Error',
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
      const levels = stringOptions
      return {
        addSubscription,
        level: null,
        maxnumber: null,
        start: null,
        end: null,
        levels
      }
    },
    methods: {
      myRule (val) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            resolve(!!val || '* Required')
          }, 1000)
        })
      },
      filterFn (val, update) {
      if (val === '') {
        update(() => {
          this.levels = stringOptions
        })
        return
      }

      update(() => {
        const needle = val.toLowerCase()
        this.levels = stringOptions.filter(v => v.toLowerCase().indexOf(needle) > -1)
      })
    }
    }
  }
  </script>
  