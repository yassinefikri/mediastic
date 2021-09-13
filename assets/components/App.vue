<template>
  <div>
    <navbar/>
    <router-view></router-view>
  </div>
</template>

<script>
import axios, * as others from 'axios';
import Navbar from './NavBar';
import Home from './Home'
import Account from './Account'

export default {
  name: "app",
  components: {Navbar, Home, Account},
  data() {
    return {
      alerts: [],
    }
  },
  mounted() {
    axios
        .get(Routing.generate('user_infos'))
        .then(response => {
          this.$store.commit('setUserInfos', response.data)
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    goBack() {
      window.history.length > 1 ? this.$router.go(-1) : this.$router.push('/')
    },
  }
}
</script>

<style scoped>

</style>
