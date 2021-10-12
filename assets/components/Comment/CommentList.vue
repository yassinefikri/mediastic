<template>
  <div class="d-flex flex-column">
    <span v-if="0 === this.list.length" class="link-secondary mx-auto">No comments found</span>
    <div v-else>
      <div class="d-flex">
        <span class="h5">Comments</span>
        <div v-if=" this.list.length > 1" class="flex-grow-1 d-flex justify-content-end">
          <button @click="toggleSort(true)" class="btn me-1"
                  :class="[sortAsc ? 'btn-info disabled' : 'btn-outline-info']">
            <i class="bi bi-sort-down"></i>
          </button>
          <button @click="toggleSort(false)" class="btn" :class="[sortAsc ? 'btn-outline-info' : 'btn-info disabled']">
            <i class="bi bi-sort-up"></i>
          </button>
        </div>
      </div>
      <comment v-for="(comment,index) in list" :key="index" :comment="comment" @comment-updated="updateComment"/>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import Comment from "./Comment"

export default {
  name: "comment-list",
  props: ['postId'],
  components: {Comment},
  data() {
    return {
      list: [],
      sortAsc: true,
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('get_post_comments', {'id': this.postId}))
        .then(response => {
          this.list = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    addComment(comment) {
      if (this.sortAsc) {
        this.list.push(comment)
      } else {
        this.list.unshift(comment)
      }
    },
    toggleSort(order) {
      if (this.sortAsc !== order) {
        this.sortAsc = order
        this.list.sort((commentA, commentB) => (this.sortAsc) ? commentA.createdAt > commentB.createdAt : commentA.createdAt < commentB.createdAt)
      }
    },
    updateComment(comment) {
      let arr = [...this.list]
      let index = arr.findIndex((temp) => (temp.id === comment.id))
      if (-1 !== index) {
        arr[index] = comment
        this.list = arr
        return true
      }
      return false
    },
    addOrUpdateComment(comment){
      if(false === this.updateComment(comment)){
        this.addComment(comment)
      }
    }
  }
}
</script>

<style scoped>

</style>