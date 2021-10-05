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
        <button class="navbar-toggler position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
          <span v-if="getNavbarButtonCount > 0"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
              getNavbarButtonCount
            }}</span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
            <div id="navbar-first-half" class="w-100 d-flex justify-content-center align-items-center">
              <div id="navbar-search-container" class="me-2">
                <navbar-search/>
              </div>
              <div id="navbar-icons-container">
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
                <router-link
                    :to="{name: 'chat'}"
                    custom
                    v-slot="{ href, route, navigate, isActive, isExactActive }">
                  <nav-icon-link :active="isActive" :href="href" @click="navigate" icon="bi-chat-fill"
                                 :count="unreadConversation">{{ route.fullPath }}
                  </nav-icon-link>
                </router-link>
                <button class="button-unstyled position-relative mx-2">
                  <i class="bi bi-bell-fill bi-25"></i>
                  <span v-if="unreadNotificationsCount > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{
                      unreadNotificationsCount
                    }}</span>
                </button>
              </div>
            </div>
            <hr/>
            <div id="navbar-second-half">
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
            </div>
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
import NavIconLink from "./NavIconLink";

export default {
  name: 'navbar',
  components: {NavLink, NavbarProfileLink, NavbarSearch, NavbarFriendshipList, NavIconLink},
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
    axios
        .get(this.$Routing.generate('get_unread_conversations'))
        .then(response => {
          if (200 === response.status) {
            this.$store.commit('setUnreadConversation', response.data)
          }
        })
        .catch(error => {
          console.log(error)
        })
  },
  computed: {
    getAvatarUrl() {
      return this.$store.getters.avatar;
    },
    getUserFirstName() {
      return this.$store.getters.firstname;
    },
    getUsername() {
      return this.$store.getters.username;
    },
    getAlert() {
      return this.$store.state.alert;
    },
    getFriendships() {
      return Object.values(this.$store.state.friendships)
    },
    getFriendshipsCount() {
      return this.getFriendships.filter(friendship => friendship.sender.username !== this.getUsername).length;
    },
    unreadConversation() {
      return Object.entries(this.$store.getters.unreadConversations).length
    },
    unreadNotificationsCount() {
      return this.$store.state.unreadNotificationsCount;
    },
    getNavbarButtonCount() {
      return this.unreadConversation + this.getFriendshipsCount + this.unreadNotificationsCount
    }
  },
  watch: {
    '$route': function (val, oldVal) {
      this.$store.commit('deleteAlert')
      this.$root.$emit('bv::hide::popover')
      if ('chat_user' === val.name && 'chat_user' !== oldVal.name) {
        this.$store.commit('resetUnreadConversation', val.params.conversationId)
      }
    },
  }
}
</script>

<style scoped>

</style>
