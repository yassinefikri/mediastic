<template>
  <div class="container text-center">
    <h1 class="my-5">Account</h1>
    <account-bar/>
    <router-view></router-view>
  </div>
</template>

<script>
import AccountBar from "./Account/AccountBar";
export default {
  name: 'Account',
  components: {AccountBar}
}
</script>

<style scoped>

</style>