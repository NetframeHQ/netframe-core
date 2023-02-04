<template>
  <div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.calendar')
        </span>
        <h2 class="main-header-title">Calendrier</h2>
    </div>
    <ul class="nf-actions">
      <li class="nf-action">
        <a class="nf-btn" data-target="#modal-ajax" data-toggle="modal" v-bind:href="this.path" >
          <span class="svgicon btn-img">
              @include('macros.svg-icons.plus')
          </span>
          <span class="btn-txt">
            Créer un document
          </span>
        </a>
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  created() {
    this.path = '/api/'+localStorage.getItem('apiToken')+'/add'
  }
}
</script>