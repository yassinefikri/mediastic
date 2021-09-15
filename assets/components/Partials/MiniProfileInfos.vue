<template>
  <div class="mini-profile-infos my-3 text-center d-flex flex-column">
    <span class="h6 mb-0">{{ firstName }} {{ lastName }}</span>
    <span class="text-muted">@{{ username }}</span>
  </div>
</template>

<script>
export default {
  name: "mini-profile-infos",
  props: ['username', 'firstName', 'lastName']
}
</script>

<style scoped>

</style>