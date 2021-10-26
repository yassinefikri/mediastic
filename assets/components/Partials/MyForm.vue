<template>
  <div ref="form-container" @submit.prevent="formHandler" class="my-container-600 mx-auto" v-html="form"></div>
</template>

<script>
import axios from "axios"

export default {
  name: "my-form",
  props: {
    getUrl: {
      type: String,
      required: true
    },
    postUrl: {
      type: String,
      required: true
    },
    message: {
      type: String,
      required: false
    },
    clearFormAfterSubmit: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      form: null
    }
  },
  mounted() {
    axios
        .get(this.getUrl)
        .then(response => {
          this.form = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    formHandler(event) {
      let form = this.$refs['form-container'].querySelector('form')
      if (null !== form) {
        let formData = new FormData(form)
        if (undefined !== event.submitter) {
          formData.append(event.submitter.name, '')
        }
        this.toogleForm(form, true)
        axios
            .post(this.postUrl, formData)
            .then(response => {
              if (200 === response.status) {
                form.querySelectorAll("input[type=password], input[type=file]").forEach(function (input) {
                  input.value = ''
                })
                this.removeErrors(form)
                this.$emit('form-posted', response.data)
                if (undefined !== this.message) {
                  this.$store.commit('setAlert', {type: 'success', message: this.message})
                }
                if (true === this.clearFormAfterSubmit) {
                  this.clearInputs(form)
                }
              }
            })
            .catch(error => {
              if (400 === error.response.status) {
                this.fillErrors(form, error.response.data)
              }
            })
            .finally(() => {
              this.toogleForm(form, false)
            })
      }
    },
    clearInputs(form) {
      form.querySelectorAll("input:not([type='hidden']),textarea").forEach(function (input) {
        input.value = ''
      })
    },
    fillErrors(form, errors, isRoot = true) {
      if (true === isRoot) {
        this.removeErrors(form)
      }
      for (const [key, value] of Object.entries(errors)) {
        if (true === Array.isArray(value)) {
          let input = form.querySelector('#' + key)
          input.classList.add('is-invalid')
          value.forEach(function (error) {
            input.parentElement.insertAdjacentHTML('beforeend', '<div class="invalid-feedback">' + error + '</div>')
          })
        } else {
          this.fillErrors(form, value, false)
        }
      }
    },
    removeErrors(form) {
      form.querySelectorAll("input.is-invalid,textarea.is-invalid").forEach(function (input) {
        input.classList.remove('is-invalid')
      })
      form.querySelectorAll(".invalid-feedback").forEach(function (input) {
        input.remove()
      })
    },
    toogleForm(form, status) {
      form.querySelectorAll("input,textarea").forEach(function (input) {
        if (true === status) {
          input.setAttribute('disabled', status)
        } else {
          input.removeAttribute('disabled')
        }
      })
    },
    clearForm() {
      let form = this.$refs['form-container'].querySelector('form')
      this.clearInputs(form)
    }
  },
}
</script>

<style scoped>

</style>