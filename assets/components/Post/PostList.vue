<template>
  <div class="my-container-800 mx-auto">
    <post v-for="(post,index) in posts" :key="index" :post="post"/>
    <hr class="my-4"/>
  </div>
</template>

<script>
import Post from "../Post/Post";

export default {
  name: "post-list",
  props: ['posts'],
  components: {Post}
}
</script>

<style scoped>

</style>