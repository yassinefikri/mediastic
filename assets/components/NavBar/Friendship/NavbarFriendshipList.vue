<template>
  <div id="search_results">
    <ul class="list-group" v-if="list.length > 0">
      <li v-for="(friendship,index) in list" class="list-group-item d-flex align-content-center">
        <navbar-search-link-container :user="getFriendshipUser(friendship)" :key="index" class="flex-grow-1"/>
        <i class="bi bi-clock ms-1 my-auto" style="font-size: 20px"></i>
      </li>
    </ul>
    <div v-else class="list-group">
      <div class="list-group-item d-flex align-content-center">
        <i class="bi bi-person me-2" style="font-size: 25px"></i>
        <span class="fs-6 my-auto">No friend requests</span>
      </div>
    </div>
  </div>
</template>

<script>
import NavbarSearchLinkContainer from "../Search/NavbarSearchLinkContainer";

export default {
  name: "navbar-friendship-list",
  props: ['list'],
  components: {NavbarSearchLinkContainer},
  methods: {
    getFriendshipUser(friendship) {
      return friendship.sender.username === this.getUsername ? friendship.receiver : friendship.sender
    }
  },
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username'];
    },
  }
}
</script>

<style scoped>

</style>