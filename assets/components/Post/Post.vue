<template>
  <div class="bg-light p-4 my-3 post-radius">
    <div class="post-owner mb-4">
      <div class="d-flex" >
        <b-popover :target="'post-'+post.id" triggers="hover" :placement="'left'">
            <mini-profile :user="post.createdBy"/>
        </b-popover>
        <div class="d-flex" :id="'post-'+post.id">
          <b-popover :target="'post-'+post.id" triggers="hover" :placement="'left'">
            <mini-profile :user="post.createdBy"/>
          </b-popover>
          <div class="post-avatar me-2">
            <img :src="post.createdBy.avatar_url" :alt="post.createdBy.firstName + ' avatar'" class="rounded-circle w-100 h-100"/>
          </div>
          <div class="d-flex flex-column flex-grow-1 justify-content-center">
            <router-link :to="{ name: 'user_profile', params: { username: post.createdBy.username }}">{{ post.createdBy.firstName }} {{ post.createdBy.lastName }}</router-link>
            <i class="bi" style="font-size: 1.25rem" :class="[post.confidentiality === 'public' ? 'bi bi-globe fw-bold' : post.confidentiality === 'friends' ? 'bi-people-fill' : 'bi-shield-lock-fill']"></i>
          </div>
        </div>
        <div class="d-flex flex-grow-1">
          <span class="text-muted ms-auto">
            {{ post.createdAt | moment-ago }}
          </span>
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
import MiniProfile from "../Partials/MiniProfile";

export default {
  name: "post",
  props: ['post'],
  components: {MiniProfile}
}
</script>

<style scoped>

</style>