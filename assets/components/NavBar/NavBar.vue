<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <router-link
            :to="{name: 'default'}"
            custom
            v-slot="{ href, route, navigate, isExactActive }">
          <nav-link :brand="true" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</nav-link>
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
            <div class="w-100 d-flex justify-content-center align-items-center">
              <div class="me-2">
                <navbar-search/>
              </div>
              <div>
                <i class="bi bi-people-fill mx-2" style="font-size: 25px"></i>
                <i class="bi bi-chat-fill mx-2" style="font-size: 25px"></i>
                <i class="bi bi-bell-fill mx-2" style="font-size: 25px"></i>
              </div>
            </div>
            <router-link
                :to="{name: 'profile'}"
                custom
                v-slot="{ href, route, navigate, isActive, isExactActive }">
              <navbar-profile-link :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']" :userFirstName="getUserFirstName" :userAvatar="getAvatarUrl">{{ route.fullPath }}</navbar-profile-link>
            </router-link>
            <li class="nav-item d-flex align-content-center ms-2 border-start" id="navbar-logout">
              <a class="nav-link my-auto" :href="$Routing.generate('app_logout')"><i class="bi bi-box-arrow-right" style="font-size: 1.5rem"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div v-for="(alert,index) in getAlerts" class="alert alert-dismissible fade show text-center rounded-0 mb-0" :class="'alert-'+alert.type" role="alert">
      {{ alert.message }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="$store.commit('deleteAlert', index)">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</template>

<script>
import NavLink from './NavLink';
import NavbarProfileLink from './NavbarProfileLink';
import NavbarSearch from "./NavbarSearch";

export default {
  name: 'navbar',
  components: {NavLink, NavbarProfileLink, NavbarSearch},
  computed: {
    getAvatarUrl(){
      return this.$store.state.userInfos['avatar_url'];
    },
    getUserFirstName(){
      return this.$store.state.userInfos['firstName'];
    },
    getAlerts(){
      return this.$store.state.alerts;
    }
  },
  watch: {
    '$route': function () {
      this.$store.commit('removeAlerts')
    }
  }
}
</script>

<style scoped>

</style>
