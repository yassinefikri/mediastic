<template>
  <a class="p-0 navbar-search-link" aria-current="page" :href="href">
    <div class="d-flex align-items-center">
      <div class="small-avatar me-3">
        <img :src="user.avatar_url" :alt="user.username + '\'s avatar'" class="w-100 h-100" />
      </div>
      <div class="d-flex flex-column">
        <span class="h6 mb-0">{{ user.firstName }} {{ user.lastName }}</span>
        <span class="text-muted">@{{ user.username }}</span>
      </div>
    </div>
  </a>
</template>

<script>
export default {
  name: "navbar-search-link",
  props: ['active', 'href', 'label', 'brand', 'user'],
}
</script>

<style scoped>

</style>