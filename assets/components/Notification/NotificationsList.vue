<template>
  <div class=" my-5 my-container-500 mx-auto link-style-none">
    <div v-if="notifications.length > 0">
      <router-link
          v-for="(notif, index) in notifications"
          :key="index"
          :to="{name: 'post_view', params: {postId: notif.post.id}}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <notification-link :active="isActive" :href="href" @click="navigate" :notif="notif"/>
      </router-link>
    </div>
    <div v-else class="list-group">
      <div class="list-group-item d-flex align-content-center">
        <i class="bi bi-bell-slash me-2" style="font-size: 25px"></i>
        <span class="fs-6 my-auto">No notifications found</span>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex"
import NotificationLink from "./NotificationLink"
import axios from "axios"

export default {
  name      : "notifications-list",
  components: {NotificationLink},
  data() {
    return {
      page    : 2,
      lastPage: false
    }
  },
  computed: {
    ...mapGetters([
      'notifications',
    ]),
  },
  mounted() {
    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight
      if (true === bottomOfWindow && false === this.lastPage) {
        this.fetchNotifs()
      }
    }
  },
  methods: {
    fetchNotifs() {
      if (false === this.lastPage) {
        axios
            .get(this.$Routing.generate('getNotifications', {'page': this.page}))
            .then(response => {
              this.$store.commit('handleNotification', {data: response.data, end: true})
              if (response.data.length < 10) {
                this.lastPage = true
              } else {
                this.page++
              }
            })
            .catch(error => {
              console.log(error)
            })
      }
    }
  }
}
</script>

<style scoped>

</style>