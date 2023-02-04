<template>
    <li class="list-unstyled-item"  :class="onlyimage?'gallery-item':''">
        <template v-if="onlyimage && size > 1">
            <a class="viewMedia text-center"
                :data-media-name="media.name"
                :data-media-id="media.id"
                :data-media-type="media.type"
                :data-media-platform="media.platform"
                :data-media-mime-type="media.mime_type"
            >
                <img :src="media.thumb || media.mediaUrl" class="img-fluid" />
                <template v-if="size > 1 && currentKey > 3">
                    <span class="overlay"><p>+{{ size-3 }}</p></span>
                </template>
                <template v-else>
                    <!--  add menu //-->
                </template>
            </a>
        </template>
        <template v-else>
            <div class="tl-video-feed-contain text-center" v-if="(media.platform || media.mediaPlatform) !== 'local'">
                <div class="tl-video-feed">
                <iframe v-if="media.platform == 'youtube'" id="ytplayer" type="text/html" :src="'https://www.youtube.com/embed/'+media.file_name" width="100%" height="280px" frameborder="0" allowfullscreen></iframe>
                <iframe v-else-if="media.platform == 'dailymotion'" :src="'https://www.dailymotion.com/embed/video/'+media.file_name+'?api=true'" width="100%" height="280px" frameborder="0"></iframe>
                <iframe v-else-if="media.platform == 'vimeo'" :src="'//player.vimeo.com/video/'+media.file_name" width="100%" height="280px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                <iframe v-else-if="media.platform == 'soundcloud'" width="100%" height="280px" scrolling="no" frameborder="no" :src="'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'+ media.file_name +'&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true'"></iframe>
                </div>
            </div>
            <template v-else>
                <template v-if="media.type == 0">
                    <!-- Image -->
                    <div v-if="size == 1">
                        <div class="display-full-media full-img-center">
                            <div class="preload-size">
                                <a class="viewMedia text-center"
                                    :data-media-name="media.name"
                                    :data-media-id="media.id"
                                    :data-media-type="media.type"
                                    :data-media-platform="media.platform"
                                    :data-media-mime-type="media.mime_type"
                                >
                                    <img :src="media.url || media.mediaUrl" class="img-fluid" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <a v-else :href="media.url" target="_blank" class="viewMedia panel-document"
                        :data-media-name="media.name"
                        :data-media-id="media.id"
                        :data-media-type="media.type"
                        :data-media-platform="media.platform"
                        :data-media-mime-type="media.mime_type"
                    >
                        <div class="panel-document-head">
                            <div class="panel-document-icon">
                                <div class="panel-document-preview" :style="'background-image: url('+media.url+')'"></div>
                            </div>
                            <div class="panel-document-info">
                                <h3 class="panel-document-title">{{ media.name }}</h3>
                                <!-- <p class="panel-document-subtitle"></p> -->
                            </div>
    
                            <!-- <div class="nf-btn">
                                <span class="btn-txt">{{ trans('page.openDocument') }}</span>
                            </div> -->
    
                        </div>
                    </a>
                </template>
                <div class="tl-video-feed-contain text-center" v-else-if="media.type == 1">
                    <div class="tl-video-feed">
                        <video class="video-js vjs-default-skin" controls preload="auto" width="100%" height="300">
                            <source :src="media.url" :type="media.mime_type" />
                        </video>
                    </div>
                </div>
                <div class="tl-audio-feed text-center" v-else-if="media.type == 2">
                    <audio class="audio-js" :src="media.url" preload="auto"  controls ></audio>
                </div>
                <a v-else :href="media.url" target="_blank" class="panel-document">
                    <div class="panel-document-head">
                        <div class="panel-document-icon">
                            <div class="panel-document-preview" :style="'background-image: url('+media.url+')'"></div>
                        </div>
                        <div class="panel-document-info">
                            <h3 class="panel-document-title">{{ media.name }}</h3>
                            <p class="panel-document-subtitle"></p>
                        </div>
    
                    </div>
                </a>
            </template>
        </template>
    </li>
</template>
<script>
export default {
    props: ["media", "size", "onlyimage", "currentKey"]
}
</script>
