<template>
  <div class="py-5" @click="handleClick">
    <post-form :postId="postId"/>
  </div>
</template>

<script>
import PostForm from "./PostForm";

export default {
  name: "post-edit",
  components: {PostForm},
  props: ['postId'],
  methods: {
    handleClick(e) {
      if (true === e.target.matches('button.btn.btn-danger.post-image-delete-button')
          || true === e.target.matches('i.bi.bi-trash.post-image-delete-button-icon')) {
        this.deletePostImage(e.target.getAttribute('data-id'))
      }
    },
    deletePostImage(id) {
      let elementId = 'post-image-' + id
      this.$el.querySelectorAll('#' + elementId + ',#preview-' + elementId).forEach((element) => element.remove())
    }
  }
}
</script>

<style scoped>

</style>