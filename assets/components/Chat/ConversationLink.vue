<template>
  <li class="list-group-item" :class="{'active': active}">
    <a class="d-flex flex-column" aria-current="page" :href="href">
      <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex flex-column me-4 flex-grow-1">
          <div v-for="(participant,index2) in item.participants" :key="index2">
            <div v-if="participant.username !== username" class="d-flex my-1 align-items-center">
              <div  class="toast-avatar-container me-3">
                <img :src="participant.avatar_url" class="rounded-circle w-100 h-100" :alt="participant.firstName + ' ' + participant.lastName">
              </div>
              <span class="fw-bold">{{ participant.firstName }} {{ participant.lastName }}</span>
            </div>
          </div>
        </div>
        <div class="ms-auto my-auto">
          <span class="badge bg-danger rounded-pill my-auto" :class="[unreadCount ? 'visible' : 'invisible']">{{ unreadCount }}</span>
        </div>
      </div>
      <span v-if="item.updatedAt" class="me-2 align-self-end">{{ item.updatedAt | momentAgo }}</span>
    </a>
  </li>
</template>

<script>
import {mapGetters} from "vuex"

export default {
  name: "conversation-link",
  props: ['active', 'href', 'item', 'unreadCount'],
  computed: {
    ...mapGetters([
      'username',
    ]),
  }
}
</script>

<style scoped>

</style>