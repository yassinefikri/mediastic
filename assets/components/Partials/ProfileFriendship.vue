<template>
  <div class="text-center">
    <my-form
        :key="childKey"
        :get-url="$Routing.generate('friendship_front', {'username': username})"
        :post-url="$Routing.generate('friendship', {'username': username})"
        @form-posted="refreshForm"
    />
  </div>
</template>

<script>
import MyForm from "./MyForm";

export default {
  name: "profile-friendship",
  props: ['username'],
  components: {MyForm},
  data() {
    return {
      childKey : 0,
    }
  },
  methods: {
    refreshForm() {
      this.childKey++
      this.$parent.fetchPosts();
    }
  }
}
</script>

<style scoped>

</style>