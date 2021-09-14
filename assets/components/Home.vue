<template>
  <div>
    <my-form :getUrl="post_form_getUrl" :postUrl="post_form_postUrl" />
  </div>
</template>

<script>
import MyForm from "./MyForm";
export default {
  name: 'Home',
  components: {MyForm},
  data() {
    return {
      post_form_getUrl : this.$Routing.generate('post_front'),
      post_form_postUrl : null
    }
  },
  mounted() {

  }
}
</script>

<style scoped>

</style>