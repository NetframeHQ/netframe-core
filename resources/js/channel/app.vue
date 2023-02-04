<template>
  <div class="main-container channel-feed main-container-talk">
    <perfect-scrollbar id="channel-messages" ref="scroll">
      <section class="talk talk-channel content-feed" ref="contentFeed" id="content-feed">
        <div id="top-messages"></div>
        <div v-if="messages.length > 0">
          <template v-for="message, i in messages">
            <hr class="talk-separator-sep" v-if="i==0 || $options.filters.formatDate(messages[i-1].updated_at) != $options.filters.formatDate(message.updated_at)"  :key="message.id" >
            <div class="talk-separator" v-if="i==0 || $options.filters.formatDate(messages[i-1].updated_at) != $options.filters.formatDate(message.updated_at)"  :key="message.id" >
              <span>
                {{ message.updated_at | formatDate }}
              </span>
            </div>
            <article
              :id="'message-'+message.id"
              :class="{ 'talk-line-infos': i==0 || (messages[i-1].postAuthor.id != message.postAuthor.id || $options.filters.formatDate(messages[i-1].updated_at) != $options.filters.formatDate(message.updated_at)) }"
              class="panel panel-default topic"
              :data-newsfeed-id="message.newsFeedId"
               :key="message.id"
            >
              <div class="talk-line">
                <a :href="message.postAuthor.url" class="talk-info" :data-bg-color="message.postAuthor.initialsToColorRgb">
                  <span class="avatar" v-if="i==0 || (messages[i-1].postAuthor.id != message.postAuthor.id || $options.filters.formatDate(messages[i-1].updated_at) != $options.filters.formatDate(message.updated_at))">
                    <img v-if="message.postAuthor.image != null" :src="message.postAuthor.image" />
                    <span v-else class="user-avatar-initials size-20" :style="{ 'background-color': message.postAuthor.initialsToColor }">
                        <span class="initials-letters">
                            {{message.postAuthor.initials}}
                        </span>
                    </span>
                  </span>
                  <p class="talk-username" v-if="i==0 || (messages[i-1].postAuthor.id != message.postAuthor.id || $options.filters.formatDate(messages[i-1].updated_at) != $options.filters.formatDate(message.updated_at))" :style="{ 'color': message.postAuthor.initialsToColor }">
                    {{ message.postAuthor.firstname }} {{ message.postAuthor.name }}
                  </p>
                  <p class="talk-time">
                    {{ message.updated_at | formatTime }}
                  </p>
                </a>
                <div class="talk-content" :class="message.isShare?'is-share':''">
                  <p v-html="message.formattedContent"></p>
                  <embed-link v-for="(li, key) in message.links" :link="li" :key="key" />
                  <template v-if="!message.isShare">
                    <template v-if="message.onlyImage && message.mediasApi.length > 1">
                        <div class="shared-content">
                            <div class="panel-gallery">
                                <ul class="list-gallery">
                                    <media v-for="(med, key) in message.mediasApi.slice(0, 4)" :key="key" :currentKey="key + 1" :media="med" :size="message.mediasApi.length" :onlyimage="message.onlyImage" />
                                </ul>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <ul class="list-unstyled">
                            <media v-for="(med, key) in message.mediasApi" :key="key" :media="med" :size="message.mediasApi.length" :onlyimage="message.onlyImage" />
                        </ul>
                    </template>
                  </template>
                </div>
              </div>
            </article>
          </template>
        </div>
        <!-- ERROR HANDLING  -->
        <div v-if="waitings.length >0" class="talk-sending">
          <article class="panel panel-default topic" v-for="message in waitings" :key="message.id">
            <div class="talk-line" :class="{ 'talk-line-error': message.status == 'error' }">
              <a class="talk-info" @click="sendBack">
                <svg v-if="message.status == 'error'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="22pt" height="22pt" viewBox="0 0 22 22" version="1.1"><defs><filter id="alpha" filterUnits="objectBoundingBox" x="0%" y="0%" width="100%" height="100%"> <feColorMatrix type="matrix" in="SourceGraphic" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1 0"/></filter><mask id="mask0"> <g filter="url(#alpha)"><rect x="0" y="0" width="22" height="22" style="fill:rgb(0%,0%,0%);fill-opacity:0.980392;stroke:none;"/> </g></mask><clipPath id="clip1"> <rect x="0" y="0" width="22" height="22"/></clipPath><g id="surface5" clip-path="url(#clip1)"><path style=" stroke:none;fill-rule:nonzero;fill:rgb(85.490196%,26.666667%,32.54902%);fill-opacity:1;" d="M 19 11 C 19 6.582031 15.417969 3 11 3 C 6.582031 3 3 6.582031 3 11 C 3 15.417969 6.582031 19 11 19 C 15.417969 19 19 15.417969 19 11 Z M 19 11 "/></g></defs><g id="surface1"><use xlink:href="#surface5" mask="url(#mask0)"/><path style="fill-rule:nonzero;fill:rgb(100%,100%,100%);fill-opacity:1;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke:rgb(100%,100%,100%);stroke-opacity:1;stroke-miterlimit:4;" d="M -26.30392 18.07345 C -27.486608 18.07345 -28.448355 19.035197 -28.448355 20.204888 L -28.448355 33.019509 C -28.448355 34.189201 -27.499605 35.150947 -26.30392 35.150947 C -25.121232 35.150947 -24.172482 34.176204 -24.172482 33.019509 L -24.172482 20.204888 C -24.172482 19.0222 -25.121232 18.07345 -26.30392 18.07345 Z M -26.30392 39.413823 C -27.486608 39.413823 -28.448355 40.37557 -28.448355 41.558258 C -28.448355 42.727949 -27.486608 43.689696 -26.30392 43.689696 C -25.134229 43.689696 -24.172482 42.727949 -24.172482 41.558258 C -24.172482 40.37557 -25.121232 39.413823 -26.30392 39.413823 Z M -26.30392 39.413823 " transform="matrix(0.30056,0,0,0.30056,18.902,1.728)"/></g></svg>
                <img v-else class="talk-loading" src="./img/loading.gif" />
              </a>
              <div class="talk-content">
                <ul class="nf-actions" v-if="message.status == 'error'">
                  <li class="nf-action">
                    <a class="nf-btn btn-ico btn-nobg" href="#" @click="removeMessage(message)">
                      <span class="svgicon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill="#333" class="front" d="M17,5.99999996 C18.5976809,5.99999996 19.9036609,7.24891992 19.9949073,8.82372717 L20,8.99999996 L20,9.99999996 L23,9.99999997 C23.5522847,9.99999997 24,10.4477152 24,11 C24,11.5128358 23.6139598,11.9355071 23.1166211,11.9932722 L23,12 L22,12 L22,21 C22,22.5976809 20.7510801,23.9036609 19.1762728,23.9949073 L19,24 L11,24 C9.40231907,24 8.09633909,22.7510801 8.00509266,21.1762728 L7.99999997,21 L7.99999996,12 L6.99999996,12 C6.44771521,12 5.99999996,11.5522847 5.99999996,11 C5.99999996,10.4871641 6.38604015,10.0644928 6.88337884,10.0067277 L6.99999996,9.99999997 L9.99999996,9.99999996 L9.99999998,8.99999996 C9.99999998,7.40231908 11.24892,6.09633908 12.8237272,6.00509265 L13,5.99999996 L17,5.99999996 Z M20,12 L9.99999996,12 L9.99999997,21 C9.99999997,21.5128358 10.3860401,21.9355071 10.8833788,21.9932722 L11,22 L19,22 C19.5128359,22 19.9355071,21.6139598 19.9932722,21.1166211 L20,21 L20,12 Z M13,14 C13.5128358,14 13.9355072,14.3860402 13.9932723,14.8833789 L14,15 L14,19 C14,19.5522847 13.5522847,20 13,20 C12.4871642,20 12.0644928,19.6139598 12.0067277,19.1166211 L12,19 L12,15 C12,14.4477152 12.4477152,14 13,14 Z M17,14 C17.5128358,14 17.9355072,14.3860402 17.9932723,14.8833789 L18,15 L18,19 C18,19.5522847 17.5522848,20 17,20 C16.4871642,20 16.0644928,19.6139598 16.0067277,19.1166211 L16,19 L16,15 C16,14.4477152 16.4477153,14 17,14 Z M17,7.99999996 L13,7.99999996 C12.4871641,7.99999996 12.0644928,8.38604015 12.0067277,8.88337883 L12,8.99999996 L12,9.99999996 L18,9.99999996 L18,8.99999996 C18,8.48716411 17.6139598,8.0644928 17.1166211,8.00672769 L17,7.99999996 Z"/></svg>
                      </span>
                    </a>
                  </li>
                  <li class="nf-action">
                    <a class="nf-btn btn-ico btn-nobg" @click="sendBack">
                      <span class="svgicon ico">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill="#FFF" fill-rule="nonzero" d="M19.5,5 L18,5 C16.3431458,5 15,6.34314575 15,8 L15.0050927,8.17627279 C15.0963391,9.75108004 16.4023191,11 18,11 L18,14 L15.505,14 L15.554658,13.9102848 C16.1271813,12.7770796 15.9299692,11.370234 14.9970944,10.4373591 C13.8255216,9.26578627 11.9260266,9.26578627 10.7544537,10.4373591 L5.82862061,15.3642603 L5.65205374,15.5224883 C4.78349471,16.3148819 4.78251188,17.6819303 5.64993063,18.475572 L5.89291809,18.6978925 L10.7543925,23.5625796 C11.9260266,24.7342137 13.8255216,24.7342137 14.9970944,23.5626409 L15.1320596,23.4186513 C15.9512234,22.4857685 16.0936866,21.1660738 15.5594494,20.0986478 L15.506,20 L19.0000575,20.0000862 C21.7614479,19.9998631 24,17.7613904 24,15.0001041 L24,9.5 C24,7.01471863 21.9852814,5 19.5,5 Z" class="back"></path> <path fill="#000" d="M12.1686673,11.8515727 C12.5591916,11.4610484 13.1923566,11.4610484 13.5828809,11.8515727 C13.9433648,12.2120567 13.9710944,12.7792877 13.6660695,13.1715789 L13.5828809,13.2657863 L10.8489875,16.0003205 L19,16.000035 C19.5522711,15.9999807 19.999965,15.5522711 20,15 L20,10 C20,9.44771525 19.5522847,9 19,9 L18,9 C17.4477153,9 17,8.55228475 17,8 C17,7.44771525 17.4477153,7 18,7 L19.5,7 C20.8807119,7 22,8.11928813 22,9.5 L22,15 C22,16.6568206 20.6568206,17.9999524 19,18.0000862 L10.8489875,18.0003205 L13.5828809,20.7342137 C13.9734052,21.124738 13.9734052,21.757903 13.5828809,22.1484273 C13.1923566,22.5389516 12.5591916,22.5389516 12.1686673,22.1484273 L7.24298746,17.2223205 L7,17 L7.24298746,16.7783205 L12.1686673,11.8515727 Z" class="front"></path></svg>
                      </span>
                    </a>
                  </li>

                </ul>
                <p v-html="message.content"></p>
                <link v-for="(l, key) in message.links" :link="l" :key="key" />
                <media v-for="(med, key) in message.medias" :key="key" :media="med" :size="message.medias.length" />
              </div>
            </div>
          </article>
        </div>
      </section>
      <section class="talk talk-channel content-feed" v-if="waitings.length >0"></section>
    </perfect-scrollbar>
    <a class="fn-click-badge" data-nb="0" ref="badgeInFeed" v-on:click="scrollToBottom">0</a>
    <!-- POSTING CONTENT -->
    <div class="talk-input">
      <div class="panel panel-default nf-talk-container">
        <div class="nf-talk-docs">
          <ul class="tl-posted-medias" v-if="medias.length>0 || percentCompleted != 0">
            <li class="template-download mosaic-item col-md-2 col-3 in" v-if="percentCompleted != 0">
              <div class="mosaic-content">
                <img src="/assets/img/no-media.jpg" class="img-fluid">
              </div>
              <div class="mosaic-footer">
                <div class="progress progress-upload active" role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="percentCompleted">
                  <div class="progress-bar progress-bar-upload" :style="'width: '+percentCompleted+'%;'"></div>
                </div>
              </div>
            </li>
            <li class="nf-talk-doc" v-for="(media, key) in medias" :key="key">
              <img :src="media.type == 0 ? (media.mediaUrl || '/media/download/'+media.id+'?thumb=1') : '/assets/img/icons/file.png'">
              <p>{{media.name || media.mediaPlatform}}</p>
              <a class="fn-remove-media close" @click="removeMedia(media.id)">
                <span class="svgicon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill-rule="evenodd" d="M14 14v-2a1 1 0 0 1 2 0v2h2a1 1 0 0 1 0 2h-2v2a1 1 0 0 1-2 0v-2h-2a1 1 0 0 1 0-2h2z"></path></svg>
                </span>
              </a>
            </li>
          </ul>
        </div>
        <div class="nf-talk-content">
          <!-- LINKS -->
          <div class="imported-link nf-talk-links" v-if="Object.keys(previewLink).length>0">
            <div class="link-preview" id="import-link-602" data-id="602">
              <div class="link-visual import-loader" :style='"background-image:url(/link-preview/download/"+previewLink.linkId+");"'>
              </div>
              <div class="link-infos">
                <div class="nf-close fn-remove-link" @click="resetPreview">
                </div>
                <div class="link-info">
                  <h4 class="info-title">{{ previewLink.title }}</h4>
                  <p class="info-desc">{{ previewLink.desc }}</p>
                </div>
                <div class="link-links">
                  <a href="" target="_blank" class="link-info-url">{{ previewLink.url }}</a>
                </div>
              </div>
            </div>
          </div>

          <div class="nf-talk-input-container">
            <!-- TEXTAREA -->
            <div class="nf-talk-input">
              <vue-tribute :options="tributeOptions">
                <div contenteditable @focus="restoreSelection" @blur="saveSelection" @paste="sanitize" @input="update" @keyup.enter="handleEnter" @keydown.enter.prevent @drop.prevent="addFile" @dragover.prevent ref="content" rows="1" id= "form-post-content" placeholder="Écrivez ici..." data-placeholder="Écrivez ici..." class="talk-textarea form-control autogrow mentions input ui-autocomplete-input user-success"></div>
              </vue-tribute>
            </div>

            <ul class="nf-actions">

              <!-- ADD DOC -->
              <li class="nf-action" @click="$refs.file.click()">
                <input type="file" ref="file" style="display: none" @change="upload" multiple>
                <label class="fileinput-button nf-btn btn-ico btn-nobg" for="panel-post-input-photo">
                  <span class="btn-img svgicon">
                    <span class="svgicon">
                      <svg width="30px" height="30px" viewBox="0 0 30 30" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path class="back" d="M13.8731319,6.68026003 L7.87137261,13.1834469 C5.37709509,15.8865806 5.37612233,20.2040899 7.86909331,22.9053139 C10.446471,25.6982511 14.6968029,25.6982511 17.2742424,22.9052469 L23.2781029,16.3992524 C24.2403235,15.3572232 24.2403235,13.7281241 23.2781033,12.6854278 L23.1490786,12.5547165 C23.0167706,12.4294516 22.8748903,12.3205258 22.7259352,12.2279401 L22.676,12.199 L22.7325453,11.9989955 C23.205818,10.1688803 22.7719944,8.13323796 21.430976,6.68024938 C19.3635786,4.43994476 15.9403582,4.43985416 13.8731319,6.68026003 Z" fill="#FFFFFF" fill-rule="nonzero"></path>
                        <path class="front" d="M21.8083077,14.0417835 C21.553262,13.765407 21.13967,13.765407 20.8847876,14.0417835 L14.8810904,20.547778 C13.6055354,21.9298373 11.5380651,21.9298373 10.2626734,20.547778 C9.03451841,19.216906 8.98903119,17.090112 10.1262117,15.7002829 L10.6555015,15.1175934 L16.2663706,9.03745824 C17.0318342,8.20797491 18.2722837,8.20797491 19.0374208,9.03745824 C19.8027211,9.86658769 19.8027211,11.2107824 19.0374208,12.0400888 L13.0335603,18.5460833 C12.7785146,18.8224597 12.3649226,18.8224597 12.1098769,18.5462602 C11.8548312,18.2698837 11.8548312,17.8215239 12.1098769,17.5451474 L17.651814,11.5397094 C17.9068597,11.2633329 17.9068597,10.81515 17.6519773,10.5387735 C17.3969316,10.262397 16.9831763,10.262397 16.7281306,10.5387735 L11.1863568,16.5443885 C10.4212197,17.373518 10.4210565,18.7178896 11.1863568,19.547196 C11.9514939,20.3763255 13.1922699,20.3763255 13.957407,19.547196 L19.9612674,13.0412016 C21.2364959,11.6591422 21.2366592,9.41858176 19.9612674,8.03669934 C18.7312679,6.70382863 16.7649085,6.6562261 15.4826047,7.89373312 L9.34111267,14.5398628 C7.55334362,16.4773292 7.55334362,19.6142554 9.33882673,21.5488908 C11.1243098,23.4837031 14.0189641,23.4837031 15.8044472,21.5488908 L21.8083077,15.0428963 C22.0635166,14.7665198 22.0635166,14.318337 21.8083077,14.0417835 Z" fill="#000000" fill-rule="nonzero"></path>
                      </svg>
                    </span>
                  </span>
                </label>
              </li>

              <!-- ADD EMOJIS -->
              <li class="nf-action emoji-keyboard channels nf-action nf-emoji">
                <a class="nf-btn btn-ico btn-nobg fn-display-emojis-panel">
                  <span class="svgicon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                      <circle class="back" cx="15" cy="15" r="12" fill="#FFF"/>
                      <path class="front" fill="#000" d="M15,5 C20.5228475,5 25,9.4771525 25,15 C25,20.5228475 20.5228475,25 15,25 C9.4771525,25 5,20.5228475 5,15 C5,9.4771525 9.4771525,5 15,5 Z M15,7 C10.581722,7 7,10.581722 7,15 C7,19.418278 10.581722,23 15,23 C19.418278,23 23,19.418278 23,15 C23,10.581722 19.418278,7 15,7 Z M17.6432202,17.4205794 C17.9052428,16.934408 18.5117738,16.7526994 18.9979452,17.014722 C19.4841166,17.2767445 19.6658252,17.8832756 19.4038026,18.369447 C18.5379459,19.9760062 16.8589181,21 15,21 C13.1403046,21 11.4606887,19.9751482 10.5951664,18.3675331 C10.3333557,17.8812476 10.5153285,17.2747958 11.0016139,17.012985 C11.4878994,16.7511743 12.0943513,16.933147 12.356162,17.4194325 C12.8762458,18.3854326 13.8834521,19 15,19 C16.1160831,19 17.1229332,18.3859493 17.6432202,17.4205794 Z M11.5,15 C10.6715729,15 10,14.3284271 10,13.5 C10,12.6715729 10.6715729,12 11.5,12 C12.3284271,12 13,12.6715729 13,13.5 C13,14.3284271 12.3284271,15 11.5,15 Z M18.5,15 C17.6715729,15 17,14.3284271 17,13.5 C17,12.6715729 17.6715729,12 18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 Z"/>
                    </svg>
                  </span>
                </a>
                <div class="emojis-panel">
                  <div id="emojis-1" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
                    <ul class="nav nav-tabs ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
                      <li role="tab" v-for="(group,index) in groups" :key="index" :class="`${index == selected ? 'active ui-tabs-active ui-state-active' : ''} ui-state-default ui-corner-top`" :aria-controls="`emojis-group-${group.id}`" :aria-labelledby="`ui-id-${index}`">
                        <a :href="`#emojis-group-${group.id}`" role="tab" data-toggle="tab" class="ui-tabs-anchor" :id="`ui-id-${index}`" @click="selectTab(index)">
                          {{ group.name }}
                        </a>
                      </li>
                    </ul>
                    <div v-for="(group,key) in groups" :key="key" :class="`emojis-group ${group.order == 1 ? 'active' : ''} ui-tabs-panel ui-widget-content ui-corner-bottom ps`" :id="`emojis-group-${ group.id }`" role="tabpanel" :aria-labelledby="`ui-id-${key}`" :style="`display: ${key == selected ? 'block' : 'none'};`">
                      <ul class="list-inline" data-target="#form-post-content">
                        <li class="list-inline-item" v-for="(emoji,index) in group.emojis" :key="index">
                          <a class="fn-add-unicode" :data-id="emoji.id" :data-unicode="emoji.value" @click="addEmoji(emoji)">{{ emoji.value }}</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </li>

              <!-- SEND -->
              <li class="nf-action">
                <button type="submit" title="Cliquez pour envoyer" @click="sendMessage(true)" class="btn-channel nf-btn btn-ico">
                  <span class="btn-img svgicon">
                    <span class="svgicon fn-submit">
                      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill="#FFF" class="back" fill-rule="nonzero" d="M19.5,5 L18,5 C16.3431458,5 15,6.34314575 15,8 L15.0050927,8.17627279 C15.0963391,9.75108004 16.4023191,11 18,11 L18,14 L15.505,14 L15.554658,13.9102848 C16.1271813,12.7770796 15.9299692,11.370234 14.9970944,10.4373591 C13.8255216,9.26578627 11.9260266,9.26578627 10.7544537,10.4373591 L5.82862061,15.3642603 L5.65205374,15.5224883 C4.78349471,16.3148819 4.78251188,17.6819303 5.64993063,18.475572 L5.89291809,18.6978925 L10.7543925,23.5625796 C11.9260266,24.7342137 13.8255216,24.7342137 14.9970944,23.5626409 L15.1320596,23.4186513 C15.9512234,22.4857685 16.0936866,21.1660738 15.5594494,20.0986478 L15.506,20 L19.0000575,20.0000862 C21.7614479,19.9998631 24,17.7613904 24,15.0001041 L24,9.5 C24,7.01471863 21.9852814,5 19.5,5 Z"/> <path fill="#000" class="front" d="M12.1686673,11.8515727 C12.5591916,11.4610484 13.1923566,11.4610484 13.5828809,11.8515727 C13.9433648,12.2120567 13.9710944,12.7792877 13.6660695,13.1715789 L13.5828809,13.2657863 L10.8489875,16.0003205 L19,16.000035 C19.5522711,15.9999807 19.999965,15.5522711 20,15 L20,10 C20,9.44771525 19.5522847,9 19,9 L18,9 C17.4477153,9 17,8.55228475 17,8 C17,7.44771525 17.4477153,7 18,7 L19.5,7 C20.8807119,7 22,8.11928813 22,9.5 L22,15 C22,16.6568206 20.6568206,17.9999524 19,18.0000862 L10.8489875,18.0003205 L13.5828809,20.7342137 C13.9734052,21.124738 13.9734052,21.757903 13.5828809,22.1484273 C13.1923566,22.5389516 12.5591916,22.5389516 12.1686673,22.1484273 L7.24298746,17.2223205 L7,17 L7.24298746,16.7783205 L12.1686673,11.8515727 Z"/></svg>
                    </span>
                  </span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script src="./app.js"></script>
