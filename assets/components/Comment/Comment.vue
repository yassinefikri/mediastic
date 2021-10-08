<template>
  <div class="d-flex flex-column align-self-start post-comment link-style-none w-100 p-3 mt-3 bg-light">
    <div class="d-flex justify-content-between mb-2">
      <b-popover :target="'comment-owner-'+comment.id" triggers="hover" :placement="'left'">
        <mini-profile :user="comment.owner"/>
      </b-popover>
      <div :id="'comment-owner-'+comment.id" class="d-flex align-self-start">
        <div class="post-avatar comment-avatar me-2">
          <user-avatar :user="comment.owner"/>
        </div>
        <div class="d-flex flex-column justify-content-center">
          <router-link :to="{ name: 'user_profile', params: { username: comment.owner.username }}">
            {{ comment.owner.firstName }} {{ comment.owner.lastName }}
          </router-link>
        </div>
      </div>
      <div class="flex-grow-1 d-flex flex-column">
        <span class="text-muted ms-auto">{{ comment.createdAt | momentAgo }}</span>
        <i @click="editComment" v-if="comment.owner.username === username && false === edit"
           class="bi bi-pencil-square ms-auto d-table"
           style="font-size: 1.25rem; cursor: pointer;"></i>
      </div>
    </div>
    <p class="mb-0 multi-line-content">{{ comment.content }}</p>
    <div v-if="true === edit">
      <my-form
          :get-url="$Routing.generate('edit_comment_front', {'id': comment.id})"
          :post-url="$Routing.generate('edit_comment', {'id': comment.id})"
          @form-posted="commentUpdated"
      />
      <button class="btn btn-secondary mt-2" @click="edit = false">Cancel</button>
    </div>
  </div>
</template>

<script>
import MiniProfile from "../Partials/MiniProfile"
import UserAvatar from "../User/UserAvatar"
import {mapGetters} from "vuex"
import MyForm from "../Partials/MyForm"

export default {
  name: "comment",
  props: ['comment'],
  data() {
    return {
      edit: false
    }
  },
  computed: {
    ...mapGetters([
      'username',
    ]),
  },
  components: {MiniProfile, UserAvatar, MyForm},
  methods: {
    editComment() {
      this.edit = true
    },
    commentUpdated(comment) {
      this.edit = false
      this.$emit('comment-updated', comment)
    }
  },
}
</script>

<style scoped>

</style>