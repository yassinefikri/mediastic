<template>
  <div>
    <div class="mb-3">
      <div ref="form-container" id="conversation-search-users" class="mx-auto" v-html="form" @submit.prevent></div>
      <input type="text" form="conversation-search-form" id="conversation_search_query" name="navbar_search[query]"
             class="form-control" placeholder="search" v-model:is="currentSearch" autocomplete="off">
    </div>
    <b-popover :show.sync="show" target="conversation_search_query" triggers="input" placement="bottom">
      <div class="search_results">
        <ul class="list-group">
          <li v-for="(user,index) in result" :key="index" class="list-group-item d-flex">
            <div class="d-flex align-items-center">
              <div class="small-avatar me-3">
                <img :src="user.avatar_url" :alt="user.username + '\'s avatar'" class="w-100 h-100"/>
              </div>
              <div class="d-flex flex-column">
                <span class="h6 mb-0">{{ user.firstName }} {{ user.lastName }}</span>
                <span class="text-muted">@{{ user.username }}</span>
              </div>
            </div>
            <button @click="addToConversation(user)" class="btn btn-primary d-block my-auto ms-auto"
                    :class="{'disabled': undefined !== list[user.username]}">Add
            </button>
          </li>
        </ul>
      </div>
    </b-popover>
    <ul id="ConversationUsers">
      <li v-for="(user,index) in list" :key="index" class="list-group-item d-flex">
        <div class="d-flex align-items-center">
          <div class="small-avatar me-3">
            <img :src="user.avatar_url" :alt="user.username + '\'s avatar'" class="w-100 h-100"/>
          </div>
          <div class="d-flex flex-column">
            <span class="h6 mb-0">{{ user.firstName }} {{ user.lastName }}</span>
            <span class="text-muted">@{{ user.username }}</span>
          </div>
        </div>
        <button @click="removeUserFromList(user.username)" class="btn btn-outline-danger d-block ms-auto"><i
            class="bi bi-x-lg"></i></button>
      </li>
    </ul>
    <button class="btn btn-success" :class="{'disabled': 0 === Object.keys(list).length}" @click="createConversation">
      Create
    </button>
    <button class="btn btn-secondary" @click="resetList">Cancel</button>
    <div ref="second-form-container" id="conversation-search-form-container" class="mx-auto" v-html="secondForm"
         @submit.prevent></div>
  </div>
</template>

<script>
import MyForm from "../Partials/MyForm";
import axios from "axios";

export default {
  name: "new-conversation-form",
  components: {MyForm},
  data() {
    return {
      form: null,
      secondForm: null,
      list: {},
      show: false,
      result: [],
      currentSearch: ''
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('navbar_search_front'))
        .then(response => {
          response.data = response.data.replace('id="navbar-search-form"', 'id="conversation-search-form"')
          this.form = response.data
        })
        .catch(error => {
          console.log(error)
        })
    axios
        .get(this.$Routing.generate('get_conversation_front'))
        .then(response => {
          response.data = response.data.replace('id="navbar-search-form"', 'id="conversation-search-form"')
          this.secondForm = response.data
        })
        .catch(error => {
          console.log(error)
        })
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
            console.log(error)
          })
    },
    addToConversation(user) {
      this.reset()
      if (undefined === this.list[user.username]) {
        let obj = {...this.list}
        obj[user.username] = user
        this.list = obj
      }
    },
    createConversation() {
      this.reset()
      let usernames = [this.getUsername]
      for (const property in this.list) {
        usernames.push(this.list[property].username)
      }
      let form = this.$refs['second-form-container'].querySelector('form');
      let formData = new FormData(form)
      formData.append('search_conversation[participants]', JSON.stringify(usernames))
      axios
          .post(this.$Routing.generate('get_conversation'), formData)
          .then(response => {
            if (200 === response.status) {
              this.$store.commit('updateOrAddConversation', response.data)
              this.$router.push({name: 'chat_user', params: {
                conversationId: response.data.id
                }})
              this.resetList()
            }
          })
          .catch(error => {
            if (400 === error.response.status) {
              this.resetList()
            }
          })
    },
    reset() {
      this.show = false
      this.currentSearch = ''
    },
    resetList() {
      this.list = {}
    },
    removeUserFromList(username) {
      let obj = {...this.list}
      delete obj[username]
      this.list = obj
    }
  },
  watch: {
    currentSearch: function (val) {
      this.result = []
      if (val.length > 0) {
        this.fetchResults()
      }
    },
    result: function (val) {
      this.show = val.length > 0
    },
    '$route': function () {
      this.result = []
    }
  },
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username'];
    },
  }
}
</script>

<style scoped>

</style>