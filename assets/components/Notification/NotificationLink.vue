<template>
  <a :href="href" @click="updatedNotif" class="card mb-3" :class="{'bg-light': !notif.seen}">
    <div class="row">
      <div class="col-1 d-flex align-items-center justify-content-center p-0 ms-4">
        <div>
          <user-avatar :user="notif.triggerer"/>
        </div>
      </div>
      <div class="col p-0">
        <div class="card-body p-3">
          <p class="card-text mb-0"><b>{{ notif.triggerer.firstName }} {{ notif.triggerer.lastName }} (@{{ notif.triggerer.username }})</b> {{ notif.content }}</p>
          <p class="card-text"><small class="text-muted">{{ notif.createdAt | momentAgo}}</small></p>
        </div>
      </div>
    </div>
  </a>
</template>

<script>
import axios from "axios"
import UserAvatar from "../User/UserAvatar"

export default {
  name: "notification-link",
  components: {UserAvatar},
  props: ['notif', 'active', 'href'],
  methods : {
    updatedNotif() {
      if(false === this.notif.seen) {
        axios
            .post(this.$Routing.generate('setNotificationSeen', {'id': this.notif.id}))
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