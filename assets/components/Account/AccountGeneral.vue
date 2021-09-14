<template>
  <div>
    <div ref="form-container" v-on:submit.prevent="formHandler" class="my-container-sm mx-auto"></div>
  </div>
</template>

<script>
import axios, * as others from 'axios';

export default {
  name: "AccountGeneral",
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
      let form = this.$el.querySelector("#accountGeneralForm");
      if(null !== form) {
        let formData = new FormData(form);
        axios
            .post(this.postUrl, formData)
            .then(response => {
              if(200 === response.status){
                this.$el.querySelector("input[type=password]").value='';
                this.$store.commit('setUserInfos', response.data)
                this.$store.commit('addAlert', {type: 'success', message:'Your account has been updated'})
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