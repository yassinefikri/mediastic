<template>
  <div class="container mt-3">
    <new-post-form @new-post="fetchPosts"/>
    <hr/>
    <div class="my-container-600 mx-auto">
      <post-list :posts="posts"/>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import PostList from "./Post/PostList";
import NewPostForm from "./Post/NewPostForm";
export default {
  name: 'Home',
  components: {NewPostForm,PostList},
  data(){
    return {
      posts : [],
    }
  },
  mounted() {
    this.fetchPosts()
  },
  methods: {
    fetchPosts(){
      axios
          .get(this.$Routing.generate('home'))
          .then(response => {
            this.posts = response.data
          })
          .catch(error => {
            console.log(error)
          })
    }
  }
}
</script>

<style scoped>

</style>