<template>
  <div v-if="loaded">
    <navbar/>
    <router-view></router-view>
  </div>
</template>

<script>
import axios, * as others from 'axios';
import Navbar from './NavBar/NavBar';
import Home from './Home'
import Account from './Account'
import MyToast from "./MyBootrsrap/MyToast";
import Vue from 'vue';

export default {
  name: "app",
  components: {Navbar, Home, Account, MyToast},
  data() {
    return {
      alerts: [],
      loaded: false,
      mercureActionsMapping: {
        'friendship': {
          'regex': /^\/(.+)\/friendship/,
          'handler': this.handleMercureFriendship
        },
        'chat': {
          'regex': /^\/(.+)\/chat/,
          'handler': this.handleMercureChat
        },
        'notification': {
          'regex': /^\/(.+)\/notification/,
          'handler': this.handleMercureNotification
        }
      }
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
          const hubUrl = response.headers['link'].match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];

          // Append the topic(s) to subscribe as query parameter
          const hub = new URL(hubUrl, window.origin);
          response.data.forEach(function (topic) {
            hub.searchParams.append('topic', topic);
          })

          // Subscribe to updates
          const eventSource = new EventSource(hub, {
            withCredentials: true
          });
          eventSource.onmessage = event => this.handleMercureMessage(JSON.parse(event.data));
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    goBack() {
      window.history.length > 1 ? this.$router.go(-1) : this.$router.push('/')
    },
    handleMercureMessage(data) {
      for (const property in this.mercureActionsMapping) {
        if (true === this.mercureActionsMapping[property]['regex'].test(data.topic)) {
          this.mercureActionsMapping[property]['handler'](data)
        }
      }
    },
    handleMercureFriendship(data) {
      if ('newFriendship' === data.status) {
        this.handleNewFriendship(data)
      } else if ('refusedFriendship' === data.status) {
        this.handleRefusedFriendship(data)
      } else if ('acceptedFriendship' === data.status) {
        this.handleAcceptedFriendship(data)
      } else if ('removedFriendship' === data.status) {
        this.handleRemovedFriendship(data)
      }
    },
    handleMercureChat(data) {
      if ('newMessage' === data.status) {
        this.handleNewMessage(data)
      }
    },
    handleMercureNotification(data) {
      //console.log(data)
    },
    handleNewFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('addFriendships', [friendship])
      if (friendship.receiver.username === this.getUsername) {
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
      let user = friendship.sender.username === this.getUsername ? friendship.receiver : friendship.sender
      this.$store.commit('addFriend', [user])
      if (friendship.sender.username === this.getUsername) {
        this.toast(friendship.receiver, 'Accepted you friend request', friendship.answeredAt, 'success')
      }
    },
    handleRemovedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
      let user = friendship.sender.username === this.getUsername ? friendship.receiver : friendship.sender
      this.$store.commit('removeFriend', user)
    },
    handleNewMessage(data) {
      let message = JSON.parse(data.message)
      let conversation = this.getConversations.filter((conversation) => (conversation.id === message.conversation.id))
      if (0 === conversation.length) {
        axios
            .get(this.$Routing.generate('get_specific_conversation', {'id': message.conversation.id}))
            .then(response => {
              console.log(typeof response.data)
              this.$store.commit('addConversation', [response.data])
            })
            .catch(error => {
              console.log(error)
            })
      } else {
        this.$store.commit('moveConversationToStart', message.conversation)
      }
      if (message.sender.username === this.getUsername) {
        this.$store.commit('addMessage', [message])
      } else {
        if ('chat_user' !== this.getCurrentRoute.name || parseInt(this.getCurrentRoute.params.conversationId) !== message.conversation.id) {
          this.$store.commit('addUnreadConversation', message.conversation)
        } else {
          this.$store.commit('addMessage', [message])
          axios.
              post(this.$Routing.generate('set_message_seen', {'id': message.id}))
              .catch(error => {
                console.log(error)
              })
        }
      }
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
      });
      myToastInstance.$mount()
      this.$el.appendChild(myToastInstance.$el)
    }
  },
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username'];
    },
    getCurrentRoute() {
      return this.$route;
    },
    getConversations() {
      return this.$store.state.conversations
    }
  },
}
</script>

<style scoped>

</style>
