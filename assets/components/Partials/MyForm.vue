<template>
  <div ref="form-container" @submit.prevent="formHandler" class="my-container-600 mx-auto" v-html="form"></div>
</template>

<script>
import axios from "axios";

export default {
  name: "my-form",
  props: ['getUrl', 'postUrl', 'message', 'clearFormAfterSubmit'],
  data(){
    return {
      form : null
    }
  },
  mounted(){
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
    formHandler() {
      let form = this.$refs['form-container'].querySelector('form');
      if(null !== form) {
        let formData = new FormData(form);
        axios
            .post(this.postUrl, formData)
            .then(response => {
              if(200 === response.status){
                this.$el.querySelectorAll("input[type=password], input[type=file]").forEach(function(input){
                  input.value='';
                })
                this.$emit('form-posted', response.data);
                this.$store.commit('setAlerts', {type: 'success', message: this.message})
                if(true === this.clearFormAfterSubmit) {
                  this.clearInputs();
                }
              }
            })
            .catch(error => {
              console.log(error)
            })
      }
    },
    clearInputs(){
      this.$el.querySelectorAll("input:not([type='hidden']),textarea").forEach(function(input){
        input.value='';
      })
    },
  },
}
</script>

<style scoped>

</style>