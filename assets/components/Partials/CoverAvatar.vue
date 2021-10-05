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
  props: ['avatar', 'cover'],
  computed: {
    getAvatarUrl(){
      return this.avatar ?? this.$store.getters.avatar;
    },
    getCoverURL(){
      return this.cover ?? this.$store.getters.cover;
    }
  },
}
</script>

<style scoped>

</style>