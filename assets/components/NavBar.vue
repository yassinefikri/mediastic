<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <router-link
            :to="{name: 'default'}"
            custom
            v-slot="{ href, route, navigate, isExactActive }">
          <NavLink :brand="true" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</NavLink>
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
            <router-link
                :to="{name: 'user_account'}"
                custom
                v-slot="{ href, route, navigate, isActive, isExactActive }">
              <NavLink :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']">{{ route.fullPath }}</NavLink>
            </router-link>
            <li id="avatar-navlink" class="nav-item dropdown ms-auto d-flex align-items-center">
              <p class="me-2 mb-0">{{ getUserFirstName }}</p>
              <div class="navbar-avatar">
                <img class="w-100 h-100 rounded-circle" :src="getAvatarUrl" alt="avatar"/>
              </div>
            </li>
            <li class="nav-item d-flex align-content-center ms-2">
              <a class="nav-link my-auto" :href="routes['app_logout']['path']"><img src="/build/icons/logout.png"/></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div v-for="(alert,index) in getAlerts" class="alert alert-dismissible fade show text-center rounded-0" :class="'alert-'+alert.type" role="alert">
      {{ alert.message }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="$store.commit('deleteAlert', index)">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</template>

<script>
import NavLink from './NavLink';

export default {
  name: 'navbar',
  components: {NavLink},
  data() {
    return {
      routes: {},
    }
  },
  beforeMount() {
    let routesNames = {'app_logout': 'Log out'};
    let routesMapping = {};
    function makeRoute(routesMapping, name, path, component, label){
      routesMapping[name] = {};
      routesMapping[name]['path'] = path;
      routesMapping[name]['component'] = component;
      routesMapping[name]['label'] = label;
    }
    for (const property in routesNames) {
      makeRoute(routesMapping, property, this.$Routing.generate(property), null, routesNames[property]);
    }
    this.routes = {...routesMapping};
  },
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
}
</script>

<style scoped>

</style>
