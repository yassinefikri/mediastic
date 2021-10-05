<template>
  <div class="mini-profile">
    <div class="d-flex flex-column profile-images-container profile-cover-avatar">
      <div class="images-container position-relative">
        <div class="cover-container mini-profile">
          <img :src="user.cover_url" class="w-100 h-100"/>
        </div>
        <div class="avatar-container">
          <img :src="user.avatar_url" class="rounded-circle w-100 h-100"/>
        </div>
      </div>
      <mini-profile-infos
          :username="user.firstName"
          :first-name="user.username"
          :last-name="user.lastName"
      />
    </div>
  </div>
</template>

<script>
import MiniProfileInfos from "./MiniProfileInfos"

export default {
  name: "mini-profile",
  props: ['user'],
  components: {MiniProfileInfos}
}
</script>

<style scoped>

</style>