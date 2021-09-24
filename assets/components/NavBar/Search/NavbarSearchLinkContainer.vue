<template>
  <div>
    <router-link
        :to="{ name: 'user_profile', params: { username: user.username }}"
        custom
        v-slot="{ href, route, navigate, isExactActive }">
      <navbar-search-link :href="href" @click="navigate" :user="user">{{ route.fullPath }}</navbar-search-link>
    </router-link>
  </div>
</template>

<script>
import NavbarSearchLink from "./NavbarSearchLink";

export default {
  name: "navbar-search-link-container",
  props: ['user'],
  components: {NavbarSearchLink},
}
</script>

<style scoped>

</style>