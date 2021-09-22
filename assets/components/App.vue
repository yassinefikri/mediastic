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
          response.data.forEach(function(topic){
            hub.searchParams.append('topic', topic);
          })

          // Subscribe to updates
          const eventSource = new EventSource(hub, {
            withCredentials: true
          });
          eventSource.onmessage = event => console.log(event.data);
        });
  },
  methods: {
    goBack() {
      window.history.length > 1 ? this.$router.go(-1) : this.$router.push('/')
    },
  },
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username'];
    }
  }
}
</script>

<style scoped>

</style>
