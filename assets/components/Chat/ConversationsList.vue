<template>
  <div class="conversation-container h-100 link-style-none">
    <new-conversation-form/>
    <hr/>
    <ul class="list-group overflow-auto">
      <router-link
          v-for="(item,index) in allConversations" :key="index"
          :to="{name: 'chat_user', params: { conversationId: item.id }}"
          custom
          v-slot="{ href, route, navigate, isActive, isExactActive }">
        <conversation-link :active="isActive" :href="href" @click="navigate" :item="item"
                           :unread-count="unreadConversations[item.id]">{{ route.fullPath }}
        </conversation-link>
      </router-link>
    </ul>
  </div>
</template>

<script>
import ConversationLink from "./ConversationLink"
import NewConversationForm from "./NewConversationForm"
import {mapGetters} from "vuex"

export default {
  name: "conversations-list",
  components: {ConversationLink, NewConversationForm},
  computed: {
    ...mapGetters([
      'allConversations',
      'unreadConversations'
    ]),
  }
}
</script>

<style scoped>

</style>