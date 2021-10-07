<template>
  <div class="py-5">
    <post-form :postId="postId"/>
  </div>
</template>

<script>
import PostForm from "./PostForm";

export default {
  name: "post-edit",
  components: {PostForm},
  props: ['postId']
}
</script>

<style scoped>

</style>