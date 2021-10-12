<template>
  <div class="flex-grow-1 d-flex flex-column">
    <div class="messages-container d-flex flex-column h-100 overflow-auto px-2" ref="messages-root">
      <div v-if="true === loadedMessages" class="d-flex flex-column my-1"
           v-for="(message,index) in getMessages" :key="index">
        <span class="text-muted align-self-center"
              v-if="0 === index || ((new Date(message.sentAt).getTime() - new Date(getMessages[index - 1].sentAt).getTime())/1000) >= 60"
              v-b-tooltip.hover :title="new Date(message.sentAt).toLocaleString()">
        {{ message.sentAt | momentAgo }}
        </span>
        <message :message="message"/>
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
import MyForm from "../Partials/MyForm"
import axios from "axios"
import Message from "./Message"

export default {
  name: "message-list",
  components: {MyForm, Message},
  props: ['conversationId'],
  data() {
    return {
      loadedMessages: false,
      page: 1,
      lastPage: false,
    }
  },
  created() {
    this.scrollToBottom = true
  },
  mounted() {
    this.$store.commit('initMessages', this.conversationId)
    this.fetchMessages()
    this.$refs['messages-root'].onscroll = () => {
      if (0 === this.$refs['messages-root'].scrollTop && false === this.lastPage) {
        this.fetchMessages(false)
      }
    }
  },
  computed: {
    getMessages() {
      return this.$store.getters.messages(this.conversationId)
    },
    getUrl() {
      return this.$Routing.generate('message_sending_front')
    },
    postUrl() {
      return this.$Routing.generate('message_sending', {'id': this.conversationId})
    },
  },
  methods: {
    fetchMessages(scrollToBottom = true) {
      this.scrollToBottom = scrollToBottom
      axios
          .get(this.$Routing.generate('get_conversation_messages', {id: this.conversationId, page: this.page}))
          .then(response => {
            this.$store.commit('addMessages', {data: response.data, end: false})
            this.loadedMessages = true
            this.page++
            if (response.data.length < 15) {
              this.lastPage = true
            }
          })
          .catch(error => {
            console.log(error)
          })
          .finally(() => {
            this.scrollToBottom = true
          })
    },
    scrollDown() {
      this.$refs['messages-root'].scrollTop = this.$refs['messages-root'].scrollHeight
    },
  },
  updated() {
    if (true === this.scrollToBottom) {
      this.scrollDown()
    }
  },
  watch: {
    '$route.params.conversationId': function () {
      this.$store.commit('initMessages', this.conversationId)
      this.loadedMessages = false
      this.page = 1
      this.scrollToBottom = true
      this.fetchMessages()
      this.$store.commit('resetUnreadConversation', this.conversationId)
    },
  }
}
</script>

<style scoped>

</style>