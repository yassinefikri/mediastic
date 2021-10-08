<template>
  <div class="seen-container d-flex"
       :class="[message.sender.username === username ? 'justify-content-end' : 'justify-content-start']">
    <div v-for="(person,secondIndex) in message.seenBy"
         v-if="person.username !== username && lastSeenMessage(message.conversation.id)[person.username] === message.id"
         :key="secondIndex" class="img-container">
      <user-avatar :user="person"/>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex"
import UserAvatar from "../User/UserAvatar"

export default {
  name: "message-seen",
  props: ['message'],
  components: {UserAvatar},
  computed: {
    ...mapGetters([
      'username',
      'lastSeenMessage',
    ]),
  }
}
</script>

<style scoped>

</style>