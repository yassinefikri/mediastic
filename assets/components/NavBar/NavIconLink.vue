<template>
  <a class="p-0 navlink-icon" :class="[active ? 'active' : '']" aria-current="page" :href="href">
    <button class="button-unstyled position-relative mx-2">
      <i class="bi bi-25" :class="icon"></i>
      <span v-if="count > 0"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ count }}</span>
    </button>
  </a>
</template>

<script>
export default {
  name: "nav-icon-link",
  props: ['active', 'href', 'count', 'icon'],
}
</script>

<style scoped>

</style>