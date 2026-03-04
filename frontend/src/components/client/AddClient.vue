<template>
  <q-card style="width: 100%;">
    <q-card-section class="row bg-grey-1">
      <q-item-label class="text-h6 text-bold text-light-blue-10" style="font-family: Helvetica;">NGO REGISTRATION</q-item-label>
      <q-space></q-space>
      <q-btn flat round dense icon="close" v-close-popup></q-btn>
    </q-card-section>
    <q-separator horizontal/>
    <q-card-section>
      <q-form class="full-width">
        <q-card-section>
          <div class="q-gutter-sm">
            <div class="row q-mb-sm">
                <q-input dense outlined v-model="name" ref="name" label="Name of NGO" :rules="[ myRule ]" class="col"></q-input>
            </div>
            <div class="row q-mb-sm">
                <q-input dense outlined v-model="website" ref="website" label="Website" :rules="[ myRule ]" class="col" prefix="https://">
                </q-input>
            </div>
            <div class="row q-mb-sm">
                <q-file dense outlined method="POST" ref="logo" field-name="logo" name="logo" v-model="logo" label="Logo" :rules="[ myRule ]" accept=".jpg, .JPG, .png, .PNG, jpeg, JPEG, .gif, .GIF image/*" class="col"
                >
                <template v-slot:prepend>
                    <q-icon name="image" class="cursor-pointer"></q-icon>
                </template>
                </q-file>
            </div>
          </div>
            
          <div align="right">
            <q-btn
              @click="addOrganization"
              label="Save NGO"
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
  data () {
    const addOrganization = () => {
    var form = new FormData()
    form.append('name', this.name)
    form.append('website', this.website)
    form.append('logo', this.logo)
    if (this.name !== null) {
        axios.post(this.$urlServer + 'ngoControllers/addNGO.php', form, {
          header: {
            'content-Type': 'multipart/form-data'
          }
        }).then((res) => {
          this.$q.dialog({
            title: 'Success',
            message: ('Partner NGO successfully saved'),
            ok: {
              push: true
            },
            cancel: {
              color: 'negative'
            },
            persistent: true
          }).onOk(() => {
            this.$router.push('/client')
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
      addOrganization,
      name: null,
      website: null,
      logo: null
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
