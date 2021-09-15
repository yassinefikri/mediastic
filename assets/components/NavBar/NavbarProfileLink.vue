<template>
  <li id="avatar-navlink" class="nav-item  ms-auto">
    <a class="nav-link my-auto d-flex align-content-center" :class="[active ? 'active' : '', brand ? 'navbar-brand' : '']" aria-current="page" :href="href">
      <span class="me-2 my-auto">{{ userFirstName }}</span>
      <div class="navbar-avatar">
        <img class="w-100 h-100 rounded-circle" :src="userAvatar" alt="avatar"/>
      </div>
    </a>
  </li>
</template>

<script>
export default {
  name: "navbar-profile-link",
  props: ['active', 'href', 'label', 'brand','userFirstName', 'userAvatar']
}
</script>

<style scoped>

</style>