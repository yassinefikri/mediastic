<template>
  <div>
    <cover-avatar/>
    <new-post-form @new-post="fetchPosts"/>
    <hr/>
    <post-list :posts="posts"/>
  </div>
</template>

<script>
import PostList from "./Post/PostList";
import axios from "axios";
import CoverAvatar from "./Partials/CoverAvatar";
import NewPostForm from "./Post/NewPostForm";
export default {
  name: "Profile",
  components: {CoverAvatar,NewPostForm,PostList},
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
          .get(this.$Routing.generate('profile'))
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