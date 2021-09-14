<template>
  <div class="d-flex profile-images-container mb-5">
    <div class="cover-container">
      <img :src="coverUrl" class="w-100 h-100"/>
    </div>
    <div class="avatar-container">
      <img :src="avatarUrl" class="rounded-circle w-100 h-100"/>
    </div>
  </div>
</template>

<script>
export default {
  name: "CoverAvatar",
  props: ['coverUrl', 'avatarUrl']
}
</script>

<style scoped>

</style>