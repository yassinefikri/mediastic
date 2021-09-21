<template>
  <div>
    <div ref="form-container" id="navbar-search-form-container" class="mx-auto" v-html="form" @submit.prevent></div>
    <input type="text" form="navbar-search-form" id="navbar_search_query" name="navbar_search[query]"
           class="form-control" placeholder="search" v-model:is="username">
    <b-popover :show.sync="show" target="navbar_search_query" triggers="" placement="bottom">
      <div id="search_results">
        <ul class="list-group">
          <li v-for="(user,index) in result" class="list-group-item">
            <router-link
                :to="{ name: 'user_profile', params: { username: user.username }}"
                custom
                v-slot="{ href, route, navigate, isExactActive }">
              <navbar-search-link :brand="true" :href="href" @click="navigate" :label="route['meta']['label']" :user="user">{{ route.fullPath }}</navbar-search-link>
            </router-link>
          </li>
        </ul>
      </div>
    </b-popover>
  </div>
</template>

<script>
import axios from "axios";
import NavbarSearchLink from "./NavbarSearchLink";

export default {
  name: "navbar-search",
  components: {NavbarSearchLink},
  data() {
    return {
      form: null,
      username: '',
      result: [],
      show: false
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('navbar_search_front'))
        .then(response => {
          this.form = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
  watch: {
    username: function (val, oldVal) {
      this.result = []
      if (val.length > 0) {
        this.fetchResults(val)
      }
    },
    result: function (val, oldVal) {
      this.show = val.length > 0
    },
    '$route': function () {
      this.result = []
    }
  },
  methods: {
    fetchResults(query) {
      let form = this.$refs['form-container'].querySelector('form');
      let formData = new FormData(form);
      axios
          .post(this.$Routing.generate('navbar_search'), formData)
          .then(response => {
            if (200 === response.status) {
              this.result = [...response.data]
            }
          })
          .catch(error => {
          })
    }
  }
}
</script>

<style scoped>

</style>