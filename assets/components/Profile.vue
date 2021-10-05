<template>
  <div>
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
      <div v-if="userInfos['username'] === $store.getters.username">
        <div class="my-container-600 mx-auto">
          <div class="mx-auto d-flex justify-content-center align-items-center profile-sett btn btn-secondary"
               id="profile-setting-btn">
            <i class="bi bi-gear-fill me-2" style="font-size: 1.25rem"></i>
            <router-link :to="{ name: 'user_account'}">Account</router-link>
          </div>
        </div>
        <hr/>
        <new-post-form @new-post="initAndFetchPosts"/>
      </div>
      <div v-else>
        <profile-friendship :username="username" ref="profile-friendship-form"/>
      </div>
      <hr/>
    </div>
    <post-list
        ref="child-list"
        @finished-fetching="handleFetch"
    />
  </div>
</template>

<script>
import PostList from "./Post/PostList"
import axios from "axios"
import CoverAvatar from "./Partials/CoverAvatar"
import NewPostForm from "./Post/NewPostForm"
import MiniProfileInfos from "./Partials/MiniProfileInfos"
import NavLink from "./NavBar/NavLink"
import ProfileFriendship from "./Partials/ProfileFriendship"

export default {
  name: "profile",
  components: {CoverAvatar, MiniProfileInfos, NewPostForm, PostList, NavLink, ProfileFriendship},
  props: ['username'],
  data() {
    return {
      userInfos: null,
      page: 1,
      lastPage: false,
    }
  },
  beforeMount() {
    this.init()
  },
  mounted() {
    this.fetchPosts()
    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight
      if (true === bottomOfWindow && false === this.lastPage) {
        this.fetchPosts()
      }
    }
  },
  methods: {
    fetchPosts() {
      let route = undefined === this.username ?
          this.$Routing.generate('profile', {'page': this.page}) :
          this.$Routing.generate('user_profile', {'username': this.username, 'page': this.page})
      this.$refs['child-list'].fetchPosts(route)
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
        this.userInfos = this.$store.getters.userInfos
      }
    },
    handleFetch(payload) {
      this.lastPage = payload['lastPage']
      if (true === payload['success']) {
        this.page++
      }
    },
    initPostList() {
      this.page = 1
      this.lastPage = false
    },
    initAndFetchPosts() {
      this.initPostList()
      this.$refs['child-list'].initList()
      this.fetchPosts()
    },
    initFriendshipForm() {
      if (undefined !== this.$refs['profile-friendship-form']) {
        this.$refs['profile-friendship-form'].refreshForm()
      }
    }
  },
  watch: {
    '$route.params.username': function (username) {
      let array = [undefined, this.$store.getters.username]
      if (false === array.includes(this.userInfos['username']) || false === array.includes(username)) {
        this.init()
        this.initAndFetchPosts()
        this.initFriendshipForm()
      }
    },
    '$store.state.friends': function (val, oldVal) {
      let difference = this.$options.filters.objectDifference(val, oldVal)
      difference = val[difference] ?? oldVal[difference]
      if(undefined !== difference && this.username === difference.username) {
        this.initAndFetchPosts()
        this.initFriendshipForm()
      }
    }
  },
}
</script>

<style scoped>

</style>