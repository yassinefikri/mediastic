<template>
  <div>
    <cover-avatar :cover-url="getCoverURL" :avatar-url="getAvatarUrl"/>
    <br/><br/><br/>
    <div ref="form-container" v-on:submit.prevent="formHandler"></div>
  </div>
</template>

<script>
import axios, * as others from 'axios';
import CoverAvatar from "../CoverAvatar";

export default {
  name: "AccountAvatar",
  components: {CoverAvatar},
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