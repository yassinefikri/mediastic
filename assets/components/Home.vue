<template>
  <div class="container mt-3">
    <post-form @new-post="initAndFetchPosts"/>
    <hr/>
    <div class="my-container-600 mx-auto">
      <post-list
          ref="child-list"
          @finished-fetching="handleFetch"
      />
    </div>
  </div>
</template>

<script>
import PostList from "./Post/PostList"
import PostForm from "./Post/PostForm"

export default {
  name: 'home',
  components: {PostForm, PostList},
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