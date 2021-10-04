<template>
  <div class="flex-grow-1 d-flex flex-column">
    <div class="messages-container d-flex flex-column h-100 overflow-auto px-2" id="messages-root">
      <div v-if="true === loadedMessages" class="d-flex flex-column align-items-center my-1"
           v-for="(message,index) in getMessages" :key="index">
        <span class="text-muted"
              v-if="0 === index || ((new Date(message.sentAt).getTime() - new Date(getMessages[index - 1].sentAt).getTime())/1000) >= 60"
              v-b-tooltip.hover :title="new Date(message.sentAt).toLocaleString()">
        {{ message.sentAt | momentAgo }}
        </span>
        <div class="d-flex align-items-center justify-content-end"
             :class="[message.sender.username === getUsername ? 'align-self-end' : 'align-self-start flex-row-reverse']">
          <div class="alert message mb-0 mx-2"
               :class="[message.sender.username === getUsername ? 'alert-info' : 'alert-secondary']"
               role="alert">
            {{ message.content }}
          </div>
          <div class="message-sender-avatar rounded-circle">
            <user-avatar :user="message.sender"/>
          </div>
        </div>
        <div class="seen-container d-flex"
             :class="[message.sender.username === getUsername ? 'align-self-end' : 'align-self-start']">
          <div v-for="(person,secondIndex) in message.seenBy" :key="secondIndex" class="img-container">
            <user-avatar v-if="person.username !== getUsername && getMessagesSeens[person.username] === message.id"
                         :user="person"/>
          </div>
        </div>
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
import UserAvatar from "../User/UserAvatar";
import axios from "axios";

export default {
  name: "message-list",
  components: {MyForm, UserAvatar},
  props: ['conversationId'],
  data() {
    return {
      loadedMessages: false,
      page: 1,
    }
  },
  mounted() {
    this.$store.commit('initMessages', this.conversationId)
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
      return this.$store.state.messages[this.conversationId]
    },
    getUsername() {
      return this.$store.state.userInfos['username'];
    },
    getConversation() {
      return this.$store.state.conversations.filter((conversation) => (conversation.id = this.conversationId))[0] ?? null
    },
    getMessagesSeens() {
      return this.$store.state.lastSeenMessage[this.conversationId]
    }
  },
  methods: {
    fetchMessages() {
      axios
          .get(this.$Routing.generate('get_conversation_messages', {id: this.conversationId, page: this.page}))
          .then(response => {
            this.$store.commit('addMessages', response.data)
            this.loadedMessages = true
            this.page++
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
    },
  },
  watch: {
    '$route.params.conversationId': function () {
      this.$store.commit('initMessages', this.conversationId)
      this.loadedMessages = false
      this.page = 1
      this.fetchMessages()
      this.$store.commit('resetUnreadConversation', this.conversationId)
    },
    getMessages: function () {
      this.scrollDown()
    },
  }
}
</script>

<style scoped>

</style>