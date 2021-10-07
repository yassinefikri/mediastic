<template>
  <div class="my-container-600 mx-auto py-5">
    <div class="form-group mb-3">
      <input v-model="query" class="form-control" type="text" placeholder="search">
    </div>
    <div id="search_results">
      <ul class="list-group" v-if="Object.keys(friendships).length > 0">
        <li v-for="(friendship,index) in friendships" v-if="'' === query || true === filterResult(friendship)" class="list-group-item d-flex align-content-center">
          <navbar-search-link-container :user="getFriendshipUser(friendship)" :key="index" class="flex-grow-1"/>
          <i class="bi bi-clock ms-3 my-auto" style="font-size: 20px"></i>
        </li>
      </ul>
      <div v-else class="list-group">
        <div class="list-group-item d-flex align-content-center">
          <i class="bi bi-person me-2" style="font-size: 25px"></i>
          <span class="fs-6 my-auto">No friend requests</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NavbarSearchLinkContainer from '../NavBar/Search/NavbarSearchLinkContainer'
import {mapGetters} from 'vuex'

export default {
  name: "navbar-friendship-list",
  components: {NavbarSearchLinkContainer},
  data() {
    return {
      query: ''
    }
  },
  methods: {
    getFriendshipUser(friendship) {
      return friendship.sender.username === this.username ? friendship.receiver : friendship.sender
    },
    filterResult(friendship) {
      let user = (this.username === friendship.sender.username) ? friendship.receiver.username : friendship.sender.username
      return this.filterRegex.test(user)
    }
  },
  computed: {
    ...mapGetters([
      'username',
      'friendships'
    ]),
    filterRegex(){
      return new RegExp(this.query)
    }
  }
}
</script>

<style scoped>

</style>