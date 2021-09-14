<template>
  <my-form :getUrl="getUrl" :postUrl="postUrl"/>
</template>

<script>
import MyForm from "../MyForm";

export default {
  name: "account-general",
  props: ['getUrl', 'postUrl'],
  components: {MyForm}
}
</script>

<style scoped>

</style>