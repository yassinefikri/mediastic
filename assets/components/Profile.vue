<template>
  <div v-if="userInfos">
    <cover-avatar
        :avatar="userInfos['avatar_url']"
        :cover="userInfos['cover_url']"
    />
    <mini-profile-infos
        :username="userInfos['username']"
        :first-name="userInfos['firstName']"
        :last-name="userInfos['lastName']"
    />
    <div v-if="userInfos['username'] === $store.state.userInfos['username']">
      <new-post-form @new-post="fetchPosts"/>
      <hr/>
    </div>
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
  components: {CoverAvatar, MiniProfileInfos, NewPostForm, PostList},
  props: ['username'],
  data() {
    return {
      posts: [],
      userInfos: null
    }
  },
  beforeMount() {
    this.init()
  },
  mounted() {
    this.fetchPosts()
  },
  methods: {
    fetchPosts() {
      let route = undefined === this.username ? this.$Routing.generate('profile') : this.$Routing.generate('user_profile', {'username': this.username})
      axios
          .get(route)
          .then(response => {
            this.posts = response.data
          })
          .catch(error => {
            console.log(error)
          })
    },
    init() {
      if (undefined !== this.username) {
        axios
            .get(this.$Routing.generate('user_infos_username', {'username': this.username}))
            .then(response => {
              this.userInfos = response.data
            })
            .catch(error => {
              console.log(error)
            })
      } else {
        this.userInfos = this.$store.state.userInfos
      }
    }
  },
  watch: {
    '$route.params.username': function (username) {
      this.init()
      this.fetchPosts()
    }
  },
}
</script>

<style scoped>

</style>