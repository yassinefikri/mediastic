<template>
  <div>
    <div @mouseenter="mouseOver(true)" @mouseleave="mouseOver(false)"
         class="d-flex align-items-center justify-content-end"
         :class="[message.sender.username === username ? 'align-self-end' : 'align-self-start flex-row-reverse']">
      <i v-if="message.sender.username === username && true === hovered" @click="showEdit" class="bi bi-pencil-square"
         style="font-size: 1.25rem; cursor: pointer;"></i>
      <div class="alert message mb-0 mx-2 multi-line-content"
           :class="[message.sender.username === username ? 'alert-info' : 'alert-secondary']"
           role="alert">{{ message.content }}</div>
      <div class="message-sender-avatar rounded-circle">
        <user-avatar :user="message.sender"/>
      </div>
    </div>
    <message-seen :message="message"/>
    <div v-if="true === edit" class="float-end">
      <my-form
          :get-url="$Routing.generate('message_edit_front', {'id': message.id})"
          :post-url="$Routing.generate('edit_message', {'id': message.id})"
          @form-posted="hideEdit"
          :clear-form-after-submit="true"
          class="my-3"
      />
      <button @click="hideEdit" class="btn btn-secondary">Cancel</button>
      <hr/>
    </div>
  </div>
</template>

<script>
import UserAvatar from "../User/UserAvatar"
import {mapGetters} from "vuex"
import MessageSeen from "./MessageSeen"
import MyForm from "../Partials/MyForm";

export default {
  name: "message",
  props: ['message'],
  components: {MyForm, UserAvatar, MessageSeen},
  data() {
    return {
      hovered: false,
      edit: false,
    }
  },
  computed: {
    ...mapGetters([
      'username',
    ]),
  },
  methods: {
    mouseOver(status) {
      this.hovered = status
    },
    showEdit() {
      this.edit = true
    },
    hideEdit() {
      this.edit = false
    }
  }
}
</script>

<style scoped>

</style>