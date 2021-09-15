<template>
  <div>
    <div class="d-flex profile-images-container profile-cover-avatar">
      <div class="cover-container">
        <img :src="getCoverURL" class="w-100 h-100"/>
      </div>
      <div class="avatar-container">
        <img :src="getAvatarUrl" class="rounded-circle w-100 h-100"/>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CoverAvatar",
  computed: {
    getAvatarUrl(){
      return this.$store.state.userInfos['avatar_url'];
    },
    getCoverURL(){
      return this.$store.state.userInfos['cover_url'];
    }
  },
}
</script>

<style scoped>

</style>