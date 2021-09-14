<template>
  <div class="mb-5">
    <ul class="nav nav-pills nav-fill">
      <router-link
          :to="{name: 'user_account_general'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <NavLink :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</NavLink>
      </router-link>
      <router-link
          :to="{name: 'user_account_password'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <NavLink :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</NavLink>
      </router-link>
      <router-link
          :to="{name: 'user_account_avatar'}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <NavLink :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</NavLink>
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