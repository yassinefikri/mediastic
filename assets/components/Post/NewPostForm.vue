<template>
  <my-form
      :getUrl="$Routing.generate('post_add_front')"
      :postUrl="$Routing.generate('post_add')"
      :message="'Your post has been published'"
      :clearFormAfterSubmit="true"
      @form-posted="$emit('new-post')"
  />
</template>

<script>
import MyForm from "../Partials/MyForm";

export default {
  name: "new-post-form",
  components: {MyForm,},
  props: ['getUrl', 'postUrl'],
}
</script>

<style scoped>

</style>