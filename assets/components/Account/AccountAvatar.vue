<template>
  <div>
    <cover-avatar :cover-url="getCoverURL" :avatar-url="getAvatarUrl"/>
    <br/><br/><br/>
    <my-form :getUrl="getUrl" :postUrl="postUrl"/>
  </div>
</template>

<script>
import CoverAvatar from "../Partials/CoverAvatar";
import MyForm from "../Partials/MyForm";

export default {
  name: "account-avatar",
  components: {CoverAvatar, MyForm},
  computed: {
    getAvatarUrl(){
      return this.$store.state.userInfos['avatar_url'];
    },
    getCoverURL(){
      return this.$store.state.userInfos['cover_url'];
    }
  },
  props: ['getUrl', 'postUrl'],
}
</script>

<style scoped>

</style>