<template>
  <div class="conversation-container h-100">
    <ul class="list-group overflow-auto">
      <router-link
          v-for="(item,index) in getList" :key="index"
          :to="{name: 'chat_user', params: { conversationId: item.id }}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <conversation-link :active="isActive" :href="href" @click="navigate" :item="item"
                           :unread-count="getUnreadConversation[item.id]">{{ route.fullPath }}
        </conversation-link>
      </router-link>
    </ul>
  </div>
</template>

<script>
import ConversationLink from "./ConversationLink";

export default {
  name: "conversations-list",
  components: {ConversationLink},
  computed: {
    getUnreadConversation() {
      return this.$store.state.unreadConversation
    },
    getList() {
      return this.$store.state.conversations
    }
  }
}
</script>

<style scoped>

</style>