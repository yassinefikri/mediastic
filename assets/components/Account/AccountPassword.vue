<template>
  <my-form :getUrl="getUrl" :postUrl="postUrl"/>
</template>

<script>
import MyForm from "../Partials/MyForm";

export default {
  name: "account-password",
  props: ['getUrl', 'postUrl'],
  components: {MyForm},
}
</script>

<style scoped>

</style>