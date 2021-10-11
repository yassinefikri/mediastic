<template>
  <a :href="href" @click="updatedNotif" class="card" :class="{'bg-light': !notif.seen}">
    <div class="card-body">
      <div>{{ notif.content }}</div>
      <span class="text-muted">{{ notif.createdAt | momentAgo}}</span>
    </div>
  </a>
</template>

<script>
import axios from "axios"

export default {
  name: "notification-link",
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