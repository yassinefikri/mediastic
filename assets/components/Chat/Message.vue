<template>
  <div>
    <div class="d-flex align-items-center justify-content-end"
         :class="[message.sender.username === username ? 'align-self-end' : 'align-self-start flex-row-reverse']">
      <div class="alert message mb-0 mx-2 multi-line-content"
           :class="[message.sender.username === username ? 'alert-info' : 'alert-secondary']"
           role="alert">{{ message.content }}</div>
      <div class="message-sender-avatar rounded-circle">
        <user-avatar :user="message.sender"/>
      </div>
    </div>
    <message-seen :message="message"/>
  </div>
</template>

<script>
import UserAvatar from "../User/UserAvatar"
import {mapGetters} from "vuex"
import MessageSeen from "./MessageSeen"

export default {
  name: "message",
  props: ['message'],
  components: {UserAvatar, MessageSeen},
  computed: {
    ...mapGetters([
      'username',
    ]),
  }
}
</script>

<style scoped>

</style>