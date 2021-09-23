<template>
  <div id="search_results">
    <ul class="list-group">
      <li v-for="(friendship,index) in list" class="list-group-item d-flex align-content-center">
        <navbar-search-link-container :user="getFriendshipUser(friendship)" :key="index" class="flex-grow-1"/>
        <i v-if="'accepted' === friendship.status" class="bi bi-person-check ms-1 my-auto" style="font-size: 20px"></i>
        <i v-else-if="'pending' === friendship.status" class="bi bi-clock ms-1 my-auto" style="font-size: 20px"></i>
      </li>
    </ul>
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