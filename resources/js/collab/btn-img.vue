<template>
  <div class="svgicon btn-img" v-html="svg(name)">
  </div>
</template>

<script>
export default {
  props: {
    name: {},
  },
}
</script>
