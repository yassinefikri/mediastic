<template>
  <div class="container mt-5">
    <my-form :getUrl="$Routing.generate('post_add_front')" :postUrl="$Routing.generate('post_add')"/>
  </div>
</template>

<script>
import MyForm from "./MyForm";
export default {
  name: 'Home',
  components: {MyForm},
}
</script>

<style scoped>

</style>