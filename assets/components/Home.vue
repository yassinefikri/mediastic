<template>
  <div class="container mt-3">
    <new-post-form @new-post="initAndFetchPosts"/>
    <hr/>
    <div class="my-container-800 mx-auto">
      <post-list
          ref="child-list"
          @finished-fetching="handleFetch"
      />
    </div>
  </div>
</template>

<script>
import PostList from "./Post/PostList"
import NewPostForm from "./Post/NewPostForm"

export default {
  name: 'home',
  components: {NewPostForm, PostList},
  data() {
    return {
      page: 1,
      lastPage: false,
    }
  },
  mounted() {
    this.fetchPosts()
    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight
      if (true === bottomOfWindow && false === this.lastPage) {
        this.fetchPosts()
      }
    }
  },
  methods: {
    fetchPosts() {
      this.$refs['child-list'].fetchPosts(this.$Routing.generate('home', {'page': this.page}))
    },
    handleFetch(payload) {
      this.lastPage = payload['lastPage']
      if (true === payload['success']) {
        this.page++
      }
    },
    init() {
      this.page = 1
      this.lastPage = false
    },
    initAndFetchPosts() {
      this.init()
      this.$refs['child-list'].initList()
      this.fetchPosts()
    }
  },
}
</script>

<style scoped>

</style>