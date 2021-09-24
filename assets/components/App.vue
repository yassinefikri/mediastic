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

export default {
  name: "app",
  components: {Navbar, Home, Account},
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
        });
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

    },
    handleMercureNotification(data) {

    },
    handleNewFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      //this.$store.commit('addFriendships', [friendship])
      this.$store.commit('addFriendships', [friendship])
    },
    handleRefusedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      //this.$store.commit('removeFriendship', friendship)
      this.$store.commit('removeFriendship', friendship)
    },
    handleAcceptedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
    },
    handleRemovedFriendship(data) {
      let friendship = JSON.parse(data.friendship)
      this.$store.commit('removeFriendship', friendship)
    }
  },
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username'];
    }
  },
}
</script>

<style scoped>

</style>
