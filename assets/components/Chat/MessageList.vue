<template>
  <div class="flex-grow-1 d-flex flex-column">
    <div class="messages-container d-flex flex-column h-100 overflow-auto px-2" id="messages-root">
      <div v-for="(message,index) in getMessages" :key="index" class="alert message"
           :class="[message.sender.username === getUsername ? 'alert-info align-self-end' : 'alert-secondary align-self-start']"
           role="alert">
        {{ message.content }}
      </div>
    </div>
    <hr/>
    <my-form
        :getUrl="getUrl"
        :postUrl="postUrl"
        :clearFormAfterSubmit="true"
        ref="message-form"
    />
  </div>
</template>

<script>
import MyForm from "../Partials/MyForm";
import axios from "axios";

export default {
  name: "message-list",
  components: {MyForm},
  props: ['conversationId'],
  mounted() {
    this.fetchMessages()
  },
  computed: {
    getUrl() {
      return this.$Routing.generate('message_sending_front')
    },
    postUrl() {
      return this.$Routing.generate('message_sending', {'id': this.conversationId})
    },
    getMessages() {
      return this.$store.state.messages
    },
    getUsername() {
      return this.$store.state.userInfos['username'];
    }
  },
  methods: {
    fetchMessages() {
      axios
          .get(this.$Routing.generate('get_conversation_messages', {'id': this.conversationId}))
          .then(response => {
            this.$store.commit('addMessage', response.data)
          })
          .catch(error => {
            console.log(error)
          })
          .finally(() => {
            this.scrollDown()
          })
    },
    scrollDown() {
      let container = this.$el.querySelector("#messages-root")
      container.scrollTop = container.scrollHeight
    }
  },
  watch: {
    '$route.params.conversationId': function () {
      this.$store.commit('resetMessages')
      this.fetchMessages()
      this.$store.commit('resetUnreadConversation', this.conversationId)
    },
    '$store.state.messages': function () {
      this.scrollDown()
    },

  }
}
</script>

<style scoped>

</style>