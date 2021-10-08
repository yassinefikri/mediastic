<template>
  <div v-if="post" class="container my-container-600 mx-auto my-5">
    <post :post="post"/>
    <hr/>
    <my-form
        :get-url="$Routing.generate('comment_form_front')"
        :post-url="$Routing.generate('add_comment', {'id': this.postId})"
        @form-posted="commentPosted"
        :clearFormAfterSubmit="true"
    />
    <hr/>
    <comment-list :post-id="post.id" ref="comment-list"/>
  </div>
</template>

<script>
import axios from 'axios'
import MyForm from '../Partials/MyForm'
import Post from "./Post"
import CommentList from "../Comment/CommentList"

export default {
  name: "post-view",
  components: {MyForm, Post, CommentList},
  props: ['postId'],
  data() {
    return {
      post: null,
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('post_view', {'id': this.postId}))
        .then(response => {
          this.post = response.data
        })
        .catch(error => {
          console.log(error)
        })
  },
  methods: {
    commentPosted(comment) {
      this.$refs['comment-list'].addComment(comment)
    }
  }
}
</script>

<style scoped>

</style>