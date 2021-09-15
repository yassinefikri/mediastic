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
      <div class="my-container-600 mx-auto">
        <div class="mx-auto d-flex justify-content-center align-items-center profile-sett btn btn-secondary" id="profile-setting-btn">
          <i class="bi bi-gear-fill me-2" style="font-size: 1.25rem"></i>
          <router-link :to="{ name: 'user_account'}">Account</router-link>
        </div>
      </div>
      <hr/>
      <new-post-form @new-post="fetchPosts"/>
    </div>
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
import NavLink from "./NavBar/NavLink";

export default {
  name: "Profile",
  components: {CoverAvatar, MiniProfileInfos, NewPostForm, PostList, NavLink},
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
      let array = [undefined, this.$store.state.userInfos['username']]
      if(false === array.includes(this.userInfos['username']) || false === array.includes(username)){
        this.init()
        this.fetchPosts()
      }
    }
  },
}
</script>

<style scoped>

</style>