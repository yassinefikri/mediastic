<template>
  <div class="mb-5">
    <ul class="nav nav-pills nav-fill">
      <router-link
          :to="{name: 'user_account_general'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <nav-link :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</nav-link>
      </router-link>
      <router-link
          :to="{name: 'user_account_password'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <nav-link :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</nav-link>
      </router-link>
      <router-link
          :to="{name: 'user_account_avatar'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <nav-link :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</nav-link>
      </router-link>
    </ul>
  </div>
</template>

<script>
import NavLink from '../NavBar/NavLink';

export default {
  name: "account-bar",
  components: {NavLink}
}
</script>

<style scoped>

</style>