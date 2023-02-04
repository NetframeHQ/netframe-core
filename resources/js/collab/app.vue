<template>
  <div class="page collab-page" spellcheck="false">
    <router-view></router-view>
  </div>
</template>

<script>
import List from './index.vue'
import css from './style.css';
export default {
  components: {
    List,
  },
}
</script>
<style lang="scss" src="./app.scss"></style>