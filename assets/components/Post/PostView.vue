<template>
  <div class="container my-container-800 mx-auto my-5">
    <post v-if="post" :post="post"/>
  </div>
</template>

<script>
import Post from './Post'
import axios from "axios";

export default {
  name: "post-view",
  components: {Post},
  props: ['postId'],
  data() {
    return {
      post: null
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('post_view', {'id': this.postId}))
        .then(response => {
          this.post = response.data
        })
        .catch(error => {
          console.log(error)
        })
  }
}
</script>

<style scoped>

</style>