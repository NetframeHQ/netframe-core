import axios from 'axios';
import VueTribute from 'vue-tribute';
import Media from './media';
import EmbedLink from './link';
export default {
    data(){
        return {
            messages: [],
            lastMessageId: undefined,
            content: "",
            medias: [],
            waitings: [],
            previewLink: {},
            ogLoading: false,
            loadingMessages: false,
            percentCompleted: 0,
            tributeOptions: {
                trigger: '@',
                values: function (text, cb) {
                    axios.get(laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels&types[5]=medias&term='+text)
                    .then(response => {
                        cb(response.data);
                    });
                },
                containerClass: 'ui-autocomplete ui-front ui-menu ui-widget ui-widget-content',
                selectClass: 'ui-state-focus',
                itemClass: 'ui-menu-item',
                lookup: 'value',
                fillAttr: 'value',
                selectTemplate: function (item) {
                    if (item.original.type == 'medias') {
                        alert('is media');
                        let mediaId = item.original.id.split('-');
                        this.medias = this.medias.concat([mediaId[1]]);

                        return '';
                    }
                    else {
                        return '<span contenteditable="false" key="'+item.original.uid+'">' + item.original.label +'</span>';
                    }
                },
                menuItemTemplate: function(item) {
                    return '<a>'+ item.original.thumb+' '+ item.original.label+'</a>';
                },
            },
            groups: [],
            position: 0,
            positionWithUnicode: 0,
            range: undefined,
            sel: undefined,
            selected: 0
        }
    },
    components: {
        VueTribute, Media, EmbedLink
    },
    mounted(){
        this.$refs.content.focus()
        this.$refs.scroll.$el.scrollTop = this.$refs.scroll.$el.scrollHeight;
        this.loadMessages();
        this.$echo.private('Channel-' + this.$route.params.id).listen('PostChannel', (payload) => {
            if(payload.post.author_id != window.userId){
                this.messages.push(payload.post);
                this.markRead();
                if(this.haveToScroll()){
                    this.scrollToBottom(100);
                }
                else{
                    this.newMessageinFeed();
                }
            }
        });
        document.addEventListener('scroll', this.handleScroll, true);
        this.waitings = JSON.parse(localStorage.getItem('waitings')) || [];
        this.fetchEmojis();
        this.addTributeOptions();
        // this.colors.unshif({id: window.userId, color: 'user'})
        
        
    },
    destroyed () {
        document.removeEventListener('scroll', this.handleScroll, true);
    },
    methods: {
        addTributeOptions(){
            var that = this;
            this.tributeOptions.selectTemplate = function (item) {
                    if (item.original.type == 'medias') {
                        let mediaId = item.original.id.split('-');
                        axios.get(laroute.route('media_download', {id: mediaId[1]}) + '?returnJson=1')
                        .then(response => {
                            that.medias = that.medias.concat(response.data.files);
                        });
                        return '';
                    }
                    else {
                        let profileId = item.original.id.split('-');
                        return '<span class="user-taggued" contenteditable="false" type="'+profileId[0]+'" key="'+profileId[1]+'">' + item.original.label +'</span>';
                    }
                }
        },
        fetchEmojis(){
            axios.get('/channels/emojis')
            .then(response => {
                this.groups = response.data;
            })
        },
        sanitize(event) {
            event.preventDefault();

            let pasteStr = event.clipboardData.getData('text/html') || event.clipboardData.getData('text');

            let regex = new RegExp(/<a([^>]*?)href\s*=\s*(['"])([^\2]*?)\2\1*([^>]*?)>(.*)<\/a>/, 'i');
            while(regex.test(pasteStr)){
                let matches = pasteStr.match(regex);
                this.importUrl(matches[3]);
                pasteStr = pasteStr.replace(matches[0], matches[5]);
            }

            const html = this.$sanitize(
                    pasteStr,
                    {
                        allowedTags: ['br', 'p']
                    }
            );
            document.execCommand('insertHTML', false, (html));
        },
        addEmoji(emoji){
            this.addText(emoji.value)
        },
        handleScroll(event) {
            if(event.target.id == 'channel-messages'){
                let top = document.getElementById('top-messages').getBoundingClientRect().top;
                let currentPostion = document.getElementById('channel-messages').getBoundingClientRect().top;
                // check if have to prepend old messages
                if(top+10 > currentPostion) {
                    this.loadMessages();
                }
                // check if scrolled to bottom
                if(this.$refs.scroll.$el.scrollTop + this.$refs.scroll.$el.getBoundingClientRect().height == this.$refs.contentFeed.getBoundingClientRect().height){
                    this.updateBadgeInFeed(0);
                }
            }
        },
        haveToScroll(){
            // compute sizes of container and message div to determine if last message is visible
            if(this.$refs.scroll.$el.scrollTop + this.$refs.scroll.$el.getBoundingClientRect().height == this.$refs.contentFeed.getBoundingClientRect().height){
                return true;
            }
            return false;
        },
        addText(text, newLine = false){
            let el = document.getElementById('form-post-content');
            el.focus();
            if (window.getSelection) {
                this.sel = window.getSelection();
                if (this.sel.getRangeAt && this.sel.rangeCount) {
                    this.range = this.sel.getRangeAt(0);
                    this.range.deleteContents();
                    if(newLine){
                        this.range.insertNode(document.createElement(text));
                        this.range.collapse(false);
                        this.range.insertNode(document.createTextNode("\u00a0"));
                    }else{
                        this.range.insertNode(document.createTextNode(text));
                        this.range.collapse(false);
                    }
                }
            } else if (document.selection && document.selection.createRange) {
                document.selection.createRange().text = text;
            }
        },
        saveSelection() {
            if (window.getSelection) {
                this.sel = window.getSelection();
                if (this.sel.getRangeAt && this.sel.rangeCount) {
                    this.range = this.sel.getRangeAt(0);
                }
            } else if (document.getSelection && document.selection.createRange) {
                this.range = document.selection.createRange();
            }
        },
        restoreSelection() {
            if (this.range) {
                if (window.getSelection) {
                    this.sel = window.getSelection();
                    this.sel.removeAllRanges();
                    this.sel.addRange(this.range);
                } else if (document.getSelection && this.range.select) {
                    this.range.select();
                }
            }
        },
        loadMessages: function(){
            if(this.loadingMessages == false){
                this.loadingMessages = true;
                let before = '';
                let haveToScroll = false;
                if(this.messages.length != 0){
                    before = '/before/'+this.messages[0].updated_at;
                    let messageId = 'message-' + this.firstElement;
                    let elementToScroll = document.getElementById(messageId);
                    this.currentOffset = elementToScroll.getBoundingClientRect().top;
                }
                else{
                    haveToScroll = true;
                }
                axios.post('/channels/'+this.$route.params.id+before)
                .then(response => {
                    if(response.data.length > 0){
                        this.messages = response.data.concat(this.messages);
                        this.loadingMessages = false;
                    }
                })
                .then(response => {
                    if(before != ''){
                        this.autoScrollToDivAtCurrentIndex();
                    }
                    else{
                        this.firstElement = this.messages[0].id;
                        //this.loadMessages();
                    }
                   this.prepareMediaModal();
                   this.markRead();
                })
                .then(() => {
                    if(haveToScroll){
                        this.scrollToBottom();
                    }
                    // reset badge counter
                    channels.getUnread(this.$route.params.id);
                })
                .catch(err => {
                    this.loadingMessages = false;
                });
            }
        },
        markRead(){
            axios.get('/channels/mark-read/'+this.$route.params.id);
        },
        newMessageinFeed() {
            let nbNew = parseInt(this.$refs.badgeInFeed.innerHTML) + 1;
            this.updateBadgeInFeed(nbNew);
        },
        autoScrollToDivAtCurrentIndex() {
            let messageId = '#message-' + this.firstElement;
            let elementToScrollTo = $(messageId); //document.getElementById(messageId);
            this.$refs.scroll.$el.scrollTop = elementToScrollTo.offset().top - this.currentOffset;
            this.firstElement = this.messages[0].id;
        },
        updateBadgeInFeed(nbRead){
            this.$refs.badgeInFeed.innerHTML = nbRead;
            this.$refs.badgeInFeed.dataset.nb =  nbRead;
        },
        scrollToBottom(timeout = 0){
            let that = this;
            setTimeout(function(){
                that.$refs.scroll.$el.scrollTop = that.$refs.contentFeed.getBoundingClientRect().height + 1000;
                that.updateBadgeInFeed(0);
            }, timeout);
        },
        update(e){
            this.content = e.target.innerText;
            const content = this.content;

            if(Object.keys(this.previewLink).length==0){
                let regex = new RegExp("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?");
                if(regex.test(content)) {
                    let matches = content.match(regex);
                    if(matches.length > 0){
                        this.importUrl(matches[0]);
                    } else {
                        this.previewLink = {};
                        this.ogLoading = false;
                    }
                }
            }
        },
        importUrl(url){
            if(this.importableUrls(url)){
                axios.post('/media/import', {url: url, confidentiality: 1, postMediaModal: 1})
                .then(response => {
                    this.medias.push(response.data.import);
                });
            } else {
                this.ogLoading = true;
                axios.post('/link-preview/get-metas', {url: url}).then(data => {
                    this.previewLink = data.data;
                    if(this.previewLink.screenPath == "pending"){
                        axios.post('/link-preview/make-screenshot', {url: this.previewLink.url, linkId: this.previewLink.linkId})
                        .then(response => {
                            this.previewLink.linkId = response.data.linkId;
                        });
                    }
                    this.ogLoading = false;
                }).catch(err => {
                    this.previewLink = {};
                    this.ogLoading = false;
                });
            }
        },
        resetPreview(){
            this.previewLink = {};
        },
        sendMessage(fromTextarea = true){
            if(!this.inPost){
                this.inPost = true;
                let shouldSend = false;
                if(fromTextarea && (this.content.trim().length != 0 || Object.keys(this.previewLink).length != 0 || this.medias.length != 0)){
                    let aMessage = {
                        id: Math.random().toString(36).substring(7),
                        content: document.getElementById('form-post-content').innerHTML || "",
                        medias: this.medias || [],
                        previewLink: this.previewLink || {},
                        status: 'waiting'
                    };
                    this.waitings.push(aMessage);
                    localStorage.setItem('waitings', JSON.stringify(this.waitings));
                    this.content = "";
                    document.getElementById('form-post-content').textContent = "";
                    this.medias = [];
                    this.previewLink = {};
                    shouldSend = true;
                }
                this.inPost = false;
                if(!fromTextarea || shouldSend){
                    this.waitings.forEach(message => {
                        let regex = new RegExp(/<span\s[a-z=":# ]*type="([a-z0-9:]*)"[a-z=":# ]*key="([a-z0-9:]*)"\s[a-z=":# ]*>@([\w\s]*)<\/span>/);
                        while(regex.test(message.content)){
                            let matches = message.content.match(regex);
                            message.content = message.content.replace(matches[0], "@["+matches[3]+"]("+matches[1]+":"+matches[2]+")");
                        }
                        let mediaIds = message.medias.map(media => media.id).join(",");
                        let formData = new FormData();
                        formData.append("channel_id", this.$route.params.id);
                        formData.append("content", message.content);
                        formData.append("post_type", 'news');
                        formData.append("mediasIds", mediaIds);
                        formData.append("linksIds", message.previewLink.linkId || "");
                        formData.append("_token", document.querySelector('meta[name="_token"]').getAttribute('content'));
                        let previewLink = this.previewLink;
                        axios.post('/channels/post', formData)
                        .then(response => {
                            return response.data;
                        })
                        .then(data => {
                            this.messages.push(data.post);
                            this.waitings = this.waitings.filter(m => message.id !== m.id);
                            localStorage.setItem('waitings', JSON.stringify(this.waitings));
                        })
                        .then(() => {
                            this.scrollToBottom(100);
                        })
                        .catch(error => {
                            let index = this.waitings.findIndex(m => message.id == m.id);
                            this.waitings[index].status = 'error';
                            localStorage.setItem('waitings', JSON.stringify(this.waitings));
                        });

                    });
                }
            }
        },
        prepareMediaModal(){
            var $modalMedia = $('#viewMediaModal');
            new PlayMediaModal({
                $modal: $modalMedia,
                $modalTitle: $modalMedia.find('.modal-title'),
                $modalContent: $modalMedia.find('.modal-carousel .carousel-item'),
                $media: $('.viewMedia'),
                baseUrl: baseUrl
            });
        },
        removeMessage(message){
            this.waitings = this.waitings.filter(m => message.id !== m.id);
            localStorage.setItem('waitings', JSON.stringify(this.waitings));
        },
        sendBack(){
            this.sendMessage(false);
        },
        handleEnter(e){
            e.preventDefault();
            if(e.ctrlKey || e.shiftKey){
                this.addText('br', true);
                return false;
            }
            this.sendMessage();
            return false;
        },
        selectTab(index){
            this.selected = index;
        },
        upload(f){
            const config = {
                onUploadProgress: function(progressEvent) {
                    this.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }.bind(this),
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };
            let formData = new FormData();
            let files;
            if(Array.isArray(f))
                files = f;
            else
                files = this.$refs.file.files;
            let data = {
                specificField: '',
                confidentiality: undefined,
                profile: JSON.stringify({id: window.userId, type: "user"}),
                profileMedia: 0,
                favoriteMedia: 0,
                postMedia: 1,
                fromXplorer: 0,
                idFolder: 0,
                mediaId: '',
                replace: false,
                originalId: null
            };
            for ( let key in data ) {
                formData.append(key, data[key]);
            }
            for(let i = 0; i < files.length; i++){
                let file = files[i];
                formData.append('files[' + i + ']', file);
            }
            axios.post('/media/upload', formData, config).then(response => {
                this.percentCompleted = 0;
                this.medias = this.medias.concat(response.data.files);
            }).catch(error => {
                this.percentCompleted = 0;
            });
        },
        removeMedia(mediaId){
            this.medias = this.medias.filter(media => {
                return media.id !== mediaId;
            });
        },
        addFile(e){
            this.upload(Array.from(e.dataTransfer.files));
        },
        importableUrls(url) {
            var regYt = /^(?:https?:\/\/)?(?:(?:www|m)\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            var regVim = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
            var regDm = /^(?:(?:http|https):\/\/)?(?:www.)?(dailymotion\.com|dai\.ly)\/((video\/([^_]+))|(hub\/([^_]+)|([^\/_]+)))$/;
            var regSc = /((https:\/\/)|(http:\/\/)|(www.)|(m\.)|(\s))+(soundcloud.com\/)+[a-zA-Z0-9\-\.]+(\/)+[a-zA-Z0-9\-\.]+/;

            if (url.match(regYt) != null || url.match(regVim) != null || url.match(regDm) != null || url.match(regSc) != null) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}
