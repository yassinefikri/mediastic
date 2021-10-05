<template>
  <div class="chat-container d-flex p-4">
    <conversations-list class="mx-2"/>
    <message-list v-if="conversationId" :conversationId="conversationId" class="mx-2"/>
  </div>
</template>

<script>
import axios from "axios"
import ConversationsList from "./Chat/ConversationsList"
import MessageList from "./Chat/MessageList"
import {mapGetters} from 'vuex'

export default {
  name: "chat",
  components: {ConversationsList, MessageList},
  props: ['conversationId'],
  mounted() {
    if (0 === this.allConversations.length)
      axios
          .get(this.$Routing.generate('get_conversations'))
          .then(response => {
            this.$store.commit('addConversation', response.data)
          })
          .catch(error => {
            console.log(error)
          })
  },
  computed: {
    ...mapGetters([
      'allConversations',
    ]),
  }
}
</script>

<style scoped>

</style>