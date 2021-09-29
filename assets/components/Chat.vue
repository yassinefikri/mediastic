<template>
  <div class="chat-container d-flex p-4">
    <conversations-list :list="list" class="mx-2"/>
    <message-list v-if="conversationId" :conversationId="conversationId" class="mx-2"/>
  </div>
</template>

<script>
import axios from "axios";
import ConversationsList from "./Chat/ConversationsList";
import MessageList from "./Chat/MessageList";

export default {
  name: "chat",
  components: {ConversationsList, MessageList},
  props: ['conversationId'],
  data() {
    return {
      list: {},
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('get_conversations'))
        .then(response => {
          this.list = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
}
</script>

<style scoped>

</style>