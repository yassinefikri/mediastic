<template>
  <my-form
      :getUrl="getUrl"
      :postUrl="postUrl"
      :message="'Your account has been updated'"
      :clearFormAfterSubmit="false"
      @form-posted="reloadUserInfos"
  />
</template>

<script>
import MyForm from "../Partials/MyForm";

export default {
  name: "account-general",
  props: ['getUrl', 'postUrl'],
  components: {MyForm},
  methods: {
    reloadUserInfos(data){
      this.$store.commit('setUserInfos', data)
    }
  }
}
</script>

<style scoped>

</style>