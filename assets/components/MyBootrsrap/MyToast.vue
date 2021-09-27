<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true"
         :class="[{'text-white border-0': variant !== 'light'}, 'bg-'+variant]" style="z-index: 11">
      <div class="toast-header">
        <div class="toast-avatar-container me-2">
          <img :src="user.avatar_url" class="rounded-circle w-100 h-100" :alt="user.firstName + ' ' + user.lastName">
        </div>
        <strong class="me-auto">{{ user.firstName }} {{ user.lastName }}</strong>
        <small>{{ time|momentAgo }}</small>
        <button type="button" @click="destroySelf" class="btn-close" data-bs-dismiss="toast"
                aria-label="Close"></button>
      </div>
      <div class="toast-body">
        {{ content }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "my-toast",
  props: ['user', 'content', 'time', 'variant'],
  methods: {
    destroySelf() {
      this.$destroy()
      this.$el.parentNode.removeChild(this.$el);
    }
  },
  mounted() {
    setTimeout(() => {
      this.destroySelf()
    }, 5000);
  },
}
</script>

<style scoped>

</style>