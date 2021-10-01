<template>
  <img :src="user.avatar_url" class="rounded-circle w-100 h-100" :alt="user.firstName + ' ' + user.lastName">
</template>

<script>
export default {
  name: "user-avatar",
  props: ['user']
}
</script>

<style scoped>

</style>