<template>
  <div>
    <div class="d-flex profile-images-container mb-5">
      <div class="cover-container">
        <img :src="getCoverURL" class="w-100 h-100"/>
      </div>
      <div class="avatar-container">
        <img :src="getAvatarUrl" class="rounded-circle w-100 h-100"/>
      </div>
    </div>
    <br/><br/><br/>
    <div ref="form-container" v-on:submit.prevent="formHandler"></div>
  </div>
</template>

<script>
import axios, * as others from 'axios';

export default {
  name: "AccountAvatar",
  computed: {
    getAvatarUrl(){
      return this.$store.state.userInfos['avatar_url'];
    },
    getCoverURL(){
      return this.$store.state.userInfos['cover_url'];
    }
  },
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
      let form = this.$el.querySelector("#accountAvatarForm");
      if(null !== form) {
        let formData = new FormData(form);
        axios
            .post(this.postUrl, formData)
            .then(response => {
              if(200 === response.status){
                this.$store.commit('setUserInfos', response.data)
                this.$store.commit('addAlert', {type: 'success', message:'Your account has been updated'})
              }
            })
            .catch(error => {
              console.log(error)
            })
      }
    },
  }
}
</script>

<style scoped>

</style>