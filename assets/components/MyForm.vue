<template>
  <div ref="form-container" v-on:submit.prevent="formHandler" class="my-container-sm mx-auto"></div>
</template>

<script>
import axios from "axios";

export default {
  name: "my-form",
  props: ['getUrl', 'postUrl'],
  mounted(){
    axios
        .get(this.getUrl)
        .then(response => {
          this.$refs['form-container'].innerHTML = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    formHandler() {
      //let form = this.$el.querySelector("#accountGeneralForm");
      let form = this.$refs['form-container'].querySelector('form');
      if(null !== form) {
        let formData = new FormData(form);
        axios
            .post(this.postUrl, formData)
            .then(response => {
              if(200 === response.status){
                this.$el.querySelectorAll("input[type=password]").forEach(function(input){
                  input.value='';
                })
                this.$store.commit('setUserInfos', response.data)
                this.$store.commit('setAlerts', {type: 'success', message:'Your account has been updated'})
              }
            })
            .catch(error => {
              console.log(error)
            })
      }
    },
  },
}
</script>

<style scoped>

</style>