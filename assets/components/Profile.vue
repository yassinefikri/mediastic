<template>
  <div>
    <cover-avatar/>
    <mini-profile-infos
        :username="getUserName"
        :first-name="getFirstName"
        :last-name="getLastName"
    />
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
import MiniProfileInfos from "./Partials/MiniProfileInfos";
export default {
  name: "Profile",
  components: {CoverAvatar,MiniProfileInfos,NewPostForm,PostList},
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
  },
  computed: {
    getUserName(){
      return this.$store.state.userInfos['username'];
    },
    getFirstName(){
      return this.$store.state.userInfos['firstName'];
    },
    getLastName(){
      return this.$store.state.userInfos['lastName'];
    },
  }
}
</script>

<style scoped>

</style>