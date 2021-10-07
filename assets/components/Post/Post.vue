<template>
  <div class="bg-light p-4 my-3 post-radius">
    <div class="post-owner mb-4">
      <div class="d-flex" >
        <div class="d-flex" :id="'post-'+post.id">
          <b-popover :target="'post-'+post.id" triggers="hover" :placement="'left'">
            <mini-profile :user="post.createdBy"/>
          </b-popover>
          <div class="post-avatar me-2">
            <user-avatar :user="post.createdBy"/>
          </div>
          <div class="d-flex flex-column flex-grow-1 justify-content-center">
            <router-link :to="{ name: 'user_profile', params: { username: post.createdBy.username }}">
              {{ post.createdBy.firstName }} {{ post.createdBy.lastName }}
            </router-link>
            <i class="bi" style="font-size: 1.25rem" :class="[post.confidentiality === 'public' ? 'bi bi-globe' : post.confidentiality === 'friends' ? 'bi-people-fill' : 'bi-shield-lock-fill']"></i>
          </div>
        </div>
        <div class="d-flex flex-column ms-auto">
          <span class="text-muted ms-auto">
            {{ post.createdAt | momentAgo }}
          </span>
          <router-link :to="{ name: 'post_edit', params: { postId: post.id }}">
            <i v-if="post.createdBy.username === username" class="bi bi-pencil-square ms-auto d-table" style="font-size: 1.25rem"></i>
          </router-link>
        </div>
      </div>
    </div>
    <p class="mb-0">{{ post.content }}</p>
    <div v-if="post.postImages.length" class="post-images-cont mt-3">
      <figure class="figure post-figure mb-0" v-for="image in post.postImages">
        <img :src="$Routing.generate('post_image', {'id': image.id})" alt="post-image" class="w-100 h-100"/>
      </figure>
    </div>
  </div>
</template>

<script>
import MiniProfile from "../Partials/MiniProfile"
import UserAvatar from "../User/UserAvatar"
import {mapGetters} from "vuex";

export default {
  name: "post",
  props: ['post'],
  components: {MiniProfile, UserAvatar},
  computed: {
    ...mapGetters([
      'username',
    ]),
  }
}
</script>

<style scoped>

</style>