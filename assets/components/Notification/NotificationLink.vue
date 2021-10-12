<template>
    <a :href="href" @click="updatedNotif" class="card mb-3" :class="{'bg-light': !notif.seen}">
      <div class="d-flex">
        <div class="col-1 d-flex align-items-center justify-content-center p-0 ms-4">
          <div>
            <user-avatar :user="notif.triggerer"/>
          </div>
        </div>
        <div class="flex-grow-1 p-0">
          <div class="card-body p-3">
            <p class="card-text mb-0 fw-bold">{{ notif.triggerer.firstName }} {{ notif.triggerer.lastName }} (@{{ notif.triggerer.username }})</p>
            <p class="card-text mb-0">{{ notif.content }}</p>
            <p class="card-text"><small class="text-muted">{{ notif.createdAt | momentAgo}}</small></p>
          </div>
        </div>
        <div class="col-1 d-flex align-items-center">
          <button @click.stop.prevent="removeNotification" type="button" class="btn-close" aria-label="Close"></button>
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
    },
    removeNotification() {
      axios
          .post(this.$Routing.generate('removeNotification', {'id': this.notif.id}))
          .then(response => {
            this.$store.commit('removeNotification', this.notif.id)
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