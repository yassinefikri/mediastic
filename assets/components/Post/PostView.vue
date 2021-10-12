<template>
  <div v-if="post" class="container my-container-600 mx-auto my-5">
    <post :post="post"/>
    <hr/>
    <my-form
        :get-url="$Routing.generate('comment_form_front')"
        :post-url="$Routing.generate('add_comment', {'id': this.postId})"
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
import mercureTypesMapping from "../../mapping/mercureTypesMapping";

export default {
  name: "post-view",
  components: {MyForm, Post, CommentList},
  props: ['postId'],
  data() {
    return {
      post: null,
      eventSource: null
    }
  },
  mounted() {
    axios
        .get(this.$Routing.generate('post_view', {'id': this.postId}))
        .then(response => {
          this.post = response.data

          // Extract the hub URL from the Link header
          const hubUrl = response.headers['link'].match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1]

          // Append the topic(s) to subscribe as query parameter
          const hub = new URL(hubUrl, window.origin)
          hub.searchParams.append('topic', '/post/' + this.post.id)

          // Subscribe to updates
          this.eventSource = new EventSource(hub, {
            withCredentials: true
          })
          this.eventSource.addEventListener(mercureTypesMapping.comment, function (event) {
            this.$refs['comment-list'].addOrUpdateComment(JSON.parse(event.data))
          }.bind(this), false)
        })
        .catch(error => {
          console.log(error)
        })
  },
  destroyed() {
    this.eventSource.close()
  }
}
</script>

<style scoped>

</style>