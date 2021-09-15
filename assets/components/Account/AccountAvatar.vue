<template>
  <div>
    <cover-avatar/>
    <my-form
        :getUrl="getUrl"
        :postUrl="postUrl"
        :message="'Your account has been updated'"
        :clearFormAfterSubmit="true"
        @form-posted="reloadUserInfos"
    />
  </div>
</template>

<script>
import CoverAvatar from "../Partials/CoverAvatar";
import MyForm from "../Partials/MyForm";

export default {
  name: "account-avatar",
  components: {CoverAvatar, MyForm},
  props: ['getUrl', 'postUrl'],
  methods: {
    reloadUserInfos(data){
      this.$store.commit('setUserInfos', data)
    }
  }
}
</script>

<style scoped>

</style>