<template>
  <div class="container my-container-800 mx-auto">
    <post v-for="(post,index) in posts" :key="index" :post="post"/>
    <hr class="my-4"/>
  </div>
</template>

<script>
import Post from "../Post/Post";
import axios from "axios";

export default {
  name: "post-list",
  components: {Post},
  data() {
    return {
      posts: []
    }
  },
  methods: {
    fetchPosts(url) {
      let payload = {'success': true, 'lastPage': false}
      axios
          .get(url)
          .then(response => {
            if (response.data.length < 10) {
              payload['lastPage'] = true
            }
            this.posts = this.posts.concat(response.data)
          })
          .catch(error => {
            payload['success'] = false
            console.log(error)
          })
          .finally(() => {
            this.$emit('finished-fetching', payload)
          });
    },
    initList() {
      this.posts = []
    }
  }
}
</script>

<style scoped>

</style>