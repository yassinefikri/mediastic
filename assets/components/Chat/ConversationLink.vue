<template>
  <li class="list-group-item" :class="[active ? 'active' : '']">
    <a class="d-flex justify-content-between align-items-start" aria-current="page" :href="href">
      <div class="d-flex flex-column me-4">
        <div v-for="(participant,index2) in item.participants" :key="index2">
          <div v-if="participant.username !== getUsername" class="d-flex my-1 align-items-center">
            <div  class="toast-avatar-container me-2">
              <img :src="participant.avatar_url" class="rounded-circle w-100 h-100" :alt="participant.firstName + ' ' + participant.lastName">
            </div>
            <span class="fw-bold">{{ participant.firstName }} {{ participant.lastName }}</span>
          </div>
        </div>
      </div>
      <div class="ms-auto my-auto">
        <span v-if="item.updatedAt" class="me-2 text-muted">{{ item.updatedAt | momentAgo }}</span>
        <span class="badge bg-danger rounded-pill my-auto" :class="[unreadCount ? 'visible' : 'invisible']">{{ unreadCount }}</span>
      </div>
    </a>
  </li>
</template>

<script>
export default {
  name: "conversation-link",
  props: ['active', 'href', 'item', 'unreadCount'],
  computed: {
    getUsername() {
      return this.$store.state.userInfos['username']
    }
  }
}
</script>

<style scoped>

</style>