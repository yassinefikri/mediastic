<template>
  <div class="text-center">
    <my-form
        :key="childKey"
        :get-url="$Routing.generate('friendship_front', {'username': username})"
        :post-url="$Routing.generate('friendship', {'username': username})"
        @form-posted="refreshForm"
    />
  </div>
</template>

<script>
import MyForm from "./MyForm"
import {mapGetters} from 'vuex'

export default {
  name: "profile-friendship",
  props: ['username'],
  components: {MyForm},
  data() {
    return {
      childKey: 0,
    }
  },
  methods: {
    refreshForm() {
      this.childKey++
      window.scrollTo(0,0)
    }
  },
  computed: {
    ...mapGetters({
      currentUsername: 'username',
    }),
  },
  watch: {
    '$store.state.friendships': function (val, oldVal) {
      let difference = this.$options.filters.objectDifference(val, oldVal)
      let users = [this.username, this.currentUsername]
      difference = val[difference] ?? oldVal[difference]
      if(undefined !== difference && true === users.includes(difference.sender.username) && true === users.includes(difference.receiver.username)) {
        this.refreshForm()
      }
    },
  }

}
</script>

<style scoped>

</style>