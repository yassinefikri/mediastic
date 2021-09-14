<template>
  <div class="container mt-3">
    <my-form :getUrl="$Routing.generate('post_add_front')" :postUrl="$Routing.generate('post_add')"/>
    <div class="my-container-500 mx-auto">
      <hr/>
    </div>
  </div>
</template>

<script>
import MyForm from "./Partials/MyForm";
export default {
  name: 'Home',
  components: {MyForm},
}
</script>

<style scoped>

</style>