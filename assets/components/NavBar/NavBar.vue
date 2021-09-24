<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <router-link
            :to="{name: 'default'}"
            custom
            v-slot="{ href, route, navigate, isExactActive }">
          <nav-link :brand="true" :href="href" @click="navigate" :label="route['meta']['label']">{{
              route.fullPath
            }}
          </nav-link>
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
                <button id="navbar-friendships-button" class="button-unstyled position-relative mx-2">
                  <i class="bi bi-people-fill bi-25 "></i>
                  <span v-if="getFriendshipsCount> 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
                      getFriendshipsCount
                    }}</span>
                </button>
                <b-popover target="navbar-friendships-button" triggers="click blur" placement="bottomright">
                  <navbar-friendship-list :list="getFriendships"/>
                </b-popover>
                <button class="button-unstyled position-relative mx-2">
                  <i class="bi bi-chat-fill bi-25"></i>
                  <span v-if="unreadMessagesCount > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
                      unreadMessagesCount
                    }}</span>
                </button>
                <button class="button-unstyled position-relative mx-2">
                  <i class="bi bi-bell-fill bi-25"></i>
                  <span v-if="unreadNotificationsCount > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
                      unreadNotificationsCount
                    }}</span>
                </button>
              </div>
            </div>
            <router-link
                :to="{name: 'profile'}"
                custom
                v-slot="{ href, route, navigate, isActive, isExactActive }">
              <navbar-profile-link :active="isActive" :href="href" @click="navigate" :label="route['meta']['label']"
                                   :userFirstName="getUserFirstName" :userAvatar="getAvatarUrl">{{ route.fullPath }}
              </navbar-profile-link>
            </router-link>
            <li class="nav-item d-flex align-content-center ms-2 border-start" id="navbar-logout">
              <a class="nav-link my-auto" :href="$Routing.generate('app_logout')">
                <i class="bi bi-box-arrow-right" style="font-size: 1.5rem"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div v-if="getAlert" class="alert alert-dismissible fade show text-center rounded-0 mb-0"
         :class="'alert-'+getAlert.type" role="alert">
      {{ getAlert.message }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"
              @click="$store.commit('deleteAlert')">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</template>

<script>
import NavLink from './NavLink';
import NavbarProfileLink from './NavbarProfileLink';
import NavbarFriendshipList from "./Friendship/NavbarFriendshipList";
import axios from "axios";
import NavbarSearch from "./Search/NavbarSearch";

export default {
  name: 'navbar',
  components: {NavLink, NavbarProfileLink, NavbarSearch, NavbarFriendshipList},
  mounted() {
    axios
        .get(this.$Routing.generate('friendships', {'page': 1}))
        .then(response => {
          if (200 === response.status) {
            this.$store.commit('addFriendships', response.data)
          }
        })
        .catch(error => {
          console.log(error)
        })
  },
  computed: {
    getAvatarUrl() {
      return this.$store.state.userInfos['avatar_url'];
    },
    getUserFirstName() {
      return this.$store.state.userInfos['firstName'];
    },
    getUsername() {
      return this.$store.state.userInfos['username'];
    },
    getAlert() {
      return this.$store.state.alert;
    },
    getFriendships() {
      return Object.values(this.$store.state.friendships)
      //return this.$store.state.friendships;
    },
    getFriendshipsCount() {
      return this.getFriendships.filter(friendship => friendship.sender.username !== this.getUsername).length;
    },
    unreadMessagesCount() {
      return this.$store.state.unreadMessagesCount;
    },
    unreadNotificationsCount() {
      return this.$store.state.unreadNotificationsCount;
    }
  },
  watch: {
    '$route': function () {
      this.$store.commit('deleteAlert')
      this.$root.$emit('bv::hide::popover')
    },
    '$store.state.friendships': {
      deep: true,
      handler: function (val, oldVal) {
        let arr1 = Object.keys(val)
        let arr2 = Object.keys(oldVal)
        let difference = [
          ...arr1.filter(x => !arr2.includes(x)),
          ...arr2.filter(x => !arr1.includes(x))
        ];
        let users = [this.username, this.getCurrentUserUsername]
        difference = val[difference] ?? oldVal[difference]
        if(undefined !== difference && true === users.includes(difference.sender.username) && true === users.includes(difference.receiver.username)) {
          this.refreshForm()
        }
      }
    }
  }
}
</script>

<style scoped>

</style>
