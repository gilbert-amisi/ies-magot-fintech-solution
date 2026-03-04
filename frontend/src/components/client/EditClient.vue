<template>
    <q-card flat style="width: 100%; max-width: 1000px;">
      <q-card-section class="row bg-grey-1">
        <q-item-label class="text-h6">Updating client organisation</q-item-label>
        <q-space></q-space>
        <q-btn flat round dense icon="close" v-close-popup></q-btn>
      </q-card-section>
      <q-separator horizontal/>
      <q-card-section>
        <q-form class="full-width">
          <q-card-section>
            <div class="row q-gutter-sm">
                <div class="col">
                    <q-item-label class="text-subtitle2">About organisation</q-item-label>
                    <div class="row q-mb-sm">
                        <q-input outlined v-model="name" ref="name" label="Name of Organisation" :rules="[ myRule ]" class="col"></q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input outlined v-model="website" ref="website" label="Website" :rules="[ myRule ]" class="col" prefix="https://">
                            <template v-slot:prepend>
                            <q-icon name="public" class="cursor-pointer"></q-icon>
                        </template>
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-file outlined method="POST" ref="logo" field-name="logo" name="logo" v-model="logo" label="Logo" :rules="[ myRule ]" accept=".jpg, .JPG, .png, .PNG, jpeg, JPEG, .gif, .GIF image/*" class="col"
                        >
                        <template v-slot:prepend>
                            <q-icon name="image" class="cursor-pointer"></q-icon>
                        </template>
                        </q-file>
                    </div>
                </div>

                <div class="col">
                    <q-item-label class="text-subtitle2">About Meal Manager</q-item-label>
                    <div class="row q-mb-sm">
                        <q-input outlined :rules="[myRule]" v-model="username" ref="username" label="Name of Meal Manager" class="col" />
                    </div>
                    <div class="row q-mb-sm">
                        <q-input type="email" outlined :rules="[myRule]" v-model="email" ref="email" label="E-mail"  class="col" >
                            <template v-slot:prepend>
                            <q-icon name="email" class="cursor-pointer"> </q-icon>
                            </template>
                        </q-input>
                    </div>
                    <div class="row q-mb-sm">
                        <q-input type="tel" outlined :rules="[myRule]" v-model="phone" ref="phone" label="Phone number" mask="+### ### ### ###" class="col">
                            <template v-slot:prepend>
                            <q-icon name="phone" class="cursor-pointer"> </q-icon>
                            </template>
                        </q-input>
                    </div>
                </div>
            </div>
              
              <div align="right">
                  <q-btn
                      @click="addOrganization"
                      label="Save client"
                      color="primary"
                      icon="save"
                      outline
                      type="submit">
                  </q-btn>
              </div>
          </q-card-section>
        </q-form>
      </q-card-section>
    </q-card>
  </template>

  <script>
  import axios from 'axios'
  export default {
    props: ['idClient', 'nameClient', 'website', 'idmanager', 'namemanager', 'emailmanager', 'phonemanager'],
    data () {
      const addOrganization = () => {
      var form = new FormData()
      form.append('clientId', this.idClient)
      form.append('name', this.name)
      form.append('website', this.website)
      form.append('logo', this.logo)
      form.append('userId', this.idmanager)
      form.append('username', this.username)
      form.append('email', this.email)
      form.append('phone', this.phone)
      form.append('type', this.type)
      if (this.$refs.name.value !== null && this.username !== null && this.email !== null && this.phone !== null && this.type !== null) {
          axios.post(this.$urlServer + 'clientControllers/updateClient.php', form, {
            header: {
              'content-Type': 'multipart/form-data'
            }
          }).then((res) => {
            this.$q.dialog({
              title: 'Success',
              message: 'Client organisation successfully updated',
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
      return {
        addOrganization,
        name: this.nameClient,
        website: this.website,
        logo: null,
        username: this.namemanager,
        type: 'Meal Manager',
        email: this.emailmanager,
        phone: this.phonemanager
      }
    },
    methods: {
      myRule (val) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            resolve(!!val || '* Required')
          }, 1000)
        })
      }
    }
  }
  </script>
  