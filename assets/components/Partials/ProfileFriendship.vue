<template>
  <div class="text-center">
    <my-form
        :key="childKey"
        :get-url="$Routing.generate('friendship_front', {'username': username})"
        :post-url="$Routing.generate('friendship', {'username': username})"
        @form-posted="refreshForm"
        :message="'Friendship updated'"
    />
  </div>
</template>

<script>
import MyForm from "./MyForm";

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
      this.$parent.fetchPosts()
    }
  },
  computed: {
    getCurrentUserUsername() {
      return this.$store.state.userInfos['username']
    }
  },
  watch: {
    '$store.state.friendships': {
      deep: true,
      handler: function (val, oldVal) {
        let arr1 = Object.keys(val)
        let arr2 = Object.keys(oldVal)
        let difference = [
          ...arr1.filter(x => !arr2.includes(x)),
          ...arr2.filter(x => !arr1.includes(x))
        ];
        let users = [this.username, this.getCurrentUserUsername]
        difference = val[difference] ?? oldVal[difference]
        if(undefined !== difference && true === users.includes(difference.sender.username) && true === users.includes(difference.receiver.username)) {
          this.refreshForm()
        }
      }
    }
  }

}
</script>

<style scoped>

</style>