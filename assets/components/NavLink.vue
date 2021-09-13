<template>
  <li class="nav-item d-flex align-content-center">
    <a class="nav-link my-auto" :class="[active ? 'active' : '', brand ? 'navbar-brand' : '']" aria-current="page" :href="href">{{ label }}</a>
  </li>
</template>

<script>
export default {
  name: 'Navlink',
  props: ['active', 'href', 'label', 'brand'],
}
</script>

<style scoped>

</style>