<template>
  <div class="mini-profile">
    <div class="d-flex flex-column profile-images-container profile-cover-avatar">
      <div class="images-container position-relative">
        <div class="cover-container mb-5">
          <img :src="user.cover_url" class="w-100 h-100"/>
        </div>
        <div class="avatar-container">
          <img :src="user.avatar_url" class="rounded-circle w-100 h-100"/>
        </div>
      </div>
      <div class="mini-profile-infos my-3 text-center d-flex flex-column">
        <span class="h6 mb-0">{{ user.firstName }} {{ user.lastName }}</span>
        <span class="text-muted">@{{ user.username }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "mini-profile",
  props: ['user'],
}
</script>

<style scoped>

</style>