<template>
  <a :href="link.final_url" target="_blank" class="link-preview">
    <div class="link-visual" :style='"background-image:url(/link-preview/download/"+link.id+");"'></div>
    <div class="link-infos">
      <div class="link-info">
        <h4 class="link-info-title">{{ link.title }}</h4>
        <p class="link-info-desc">{{ link.description }}</p>
      </div>
      <div class="link-links">
        <p class="link-info-url">{{ link.url }}</p>
      </div>
    </div>
  </a>
</template>

<script>
export default {
  props: ["link"]
}
</script>
