<template>
  <div v-if="loaded">
    <navbar/>
    <router-view></router-view>
  </div>
</template>

<script>
import axios from 'axios'
import Navbar from './NavBar/NavBar'
import Home from './Home'
import Account from './Account'
import MyToast from './MyBootrsrap/MyToast'
import Vue from 'vue'
import {mapGetters} from 'vuex'
import mercureTypesMapping from "../mapping/mercureTypesMapping"
import friendshipMapping from "../mapping/friendshipMapping"
import messageMapping from "../mapping/messageMapping"

export default {
  name      : "app",
  components: {Navbar, Home, Account, MyToast},
  data() {
    return {
      alerts: [],
      loaded: false,
    }
  },
  beforeMount() {
    axios
        .get(this.$Routing.generate('user_infos'))
        .then(response => {
          this.$store.commit('setUserInfos', response.data)
          this.loaded = true
        })
        .catch(error => {
          console.log(error)
        })
  },
  mounted() {
    axios
        .get(this.$Routing.generate('user_friends'))
        .then(response => {
          this.$store.commit('addFriend', response.data)
        })
        .catch(error => {
          console.log(error)
        })
    axios
        .get(this.$Routing.generate('discover'))
        .then(response => {
          // Extract the hub URL from the Link header
          const hubUrl = response.headers['link'].match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1]

          // Append the topic(s) to subscribe as query parameter
          const hub = new URL(hubUrl, window.origin)
          response.data.forEach(function (topic) {
            hub.searchParams.append('topic', topic)
          })

          // Subscribe to updates
          const eventSource = new EventSource(hub, {
            withCredentials: true
          })

          eventSource.addEventListener(mercureTypesMapping.chat, function (event) {
            this.handleMercureChat(JSON.parse(event.data))
          }.bind(this), false)

          eventSource.addEventListener(mercureTypesMapping.friendship, function (event) {
            this.handleMercureFriendship(JSON.parse(event.data))
          }.bind(this), false)

          eventSource.addEventListener(mercureTypesMapping.seen, function (event) {
            this.handleNewMessageSeen(JSON.parse(event.data))
          }.bind(this), false)

          eventSource.addEventListener(mercureTypesMapping.notification, function (event) {
            this.handleMercureNotification(JSON.parse(event.data))
          }.bind(this), false)
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods : {
    goBack() {
      window.history.length > 1 ? this.$router.go(-1) : this.$router.push('/')
    },
    handleMercureFriendship(data) {
      if (friendshipMapping.new === data.status) {
        this.handleNewFriendship(data)
      } else if (friendshipMapping.refused === data.status) {
        this.handleRefusedFriendship(data)
      } else if (friendshipMapping.accepted === data.status) {
        this.handleAcceptedFriendship(data)
      } else if (friendshipMapping.removed === data.status) {
        this.handleRemovedFriendship(data)
      }
    },
    handleMercureChat(data) {
      if (messageMapping.new === data.status) {
        this.handleNewMessage(data)
      } else if (messageMapping.edited === data.status) {
        this.updateMessage(data)
      }
    },
    handleMercureNotification(data) {
      this.$store.commit('handleNotification', [JSON.parse(data)])
    },
    handleNewMessageSeen(data) {
      if ('chat_user' === this.getCurrentRoute.name && "" + this.getCurrentRoute.params.conversationId === Object.keys(data)[0]) {
        this.$store.commit('setMessageSeen', data)
      }
    },
    handleNewFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('addFriendships', [friendship])
      if (friendship.receiver.username === this.username) {
        this.toast(friendship.sender, 'Sent you a friend request', friendship.sentAt, 'primary')
      }
    },
    handleRefusedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
    },
    handleAcceptedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
      let user = friendship.sender.username === this.username ? friendship.receiver : friendship.sender
      this.$store.commit('addFriend', [user])
      if (friendship.sender.username === this.username) {
        this.toast(friendship.receiver, 'Accepted you friend request', friendship.answeredAt, 'success')
      }
    },
    handleRemovedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
      let user = friendship.sender.username === this.username ? friendship.receiver : friendship.sender
      this.$store.commit('removeFriend', user)
    },
    handleNewMessage(data) {
      let message = JSON.parse(data.message)
      let conversation = this.allConversations.filter((tempconversation) => (tempconversation.id === message.conversation.id))
      if (0 === conversation.length) {
        axios
            .get(this.$Routing.generate('get_specific_conversation', {'id': message.conversation.id}))
            .then(response => {
              this.$store.commit('addConversation', [response.data])
            })
            .catch(error => {
              console.log(error)
            })
      } else {
        this.$store.commit('moveConversationToStart', message.conversation)
      }
      if (message.sender.username === this.username) {
        this.$store.commit('addMessages', [message])
      } else {
        if ('chat_user' !== this.getCurrentRoute.name || parseInt(this.getCurrentRoute.params.conversationId) !== message.conversation.id) {
          this.$store.commit('addUnreadConversation', message.conversation)
        } else {
          this.$store.commit('addMessages', [message])
          axios.post(this.$Routing.generate('set_message_seen', {'id': message.id}))
              .catch(error => {
                console.log(error)
              })
        }
      }
    },
    updateMessage(data) {
      let message = JSON.parse(data.message)
      this.$store.commit('updateMessage', message)
    },
    toast(user, content, time, variant = 'light') {
      const myToastClass = Vue.extend(MyToast)
      const myToastInstance = new myToastClass({
        propsData: {
          user,
          content,
          time,
          variant
        }
      })
      myToastInstance.$mount()
      this.$el.appendChild(myToastInstance.$el)
    }
  },
  computed: {
    ...mapGetters([
      'username',
      'allConversations',
    ]),
    getCurrentRoute() {
      return this.$route
    },
  },
}
</script>

<style scoped>

</style>
