<template>
<div class="cc">
  <div class="editor">
    <template v-if="editor && !loading">

      <editor-menu-bubble :editor="editor" :keep-in-bounds="keepInBounds" v-slot="{ commands, isActive, getMarkAttrs, menu }">
        <div
          class="menububble"
          :class="{ 'is-active': menu.isActive }"
          :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
        >
          <button
            class="menububble__button"
            :class="{ 'is-active': isActive.bold() }"
            @click="commands.bold"
            title="Gras"
          >
            <icon name="bold" />
          </button>

          <button
            class="menububble__button"
            :class="{ 'is-active': isActive.italic() }"
            @click="commands.italic"
            title="Italic"
          >
            <icon name="italic" />
          </button>

          <button
            class="menububble__button"
            :class="{ 'is-active': isActive.code() }"
            @click="commands.code"
            title="Code"
          >
            <icon name="code" />
          </button>

           <form class="menububble__form" v-if="linkMenuIsActive" @submit.prevent="setLinkUrl(commands.link, linkUrl)">
              <input class="menububble__input" type="text" v-model="linkUrl" placeholder="https://" ref="linkInput" @keydown.esc="hideLinkMenu"/>
              <button class="menububble__button" @click="setLinkUrl(commands.link, null)" type="button">
                <icon name="remove" />
              </button>
            </form>

            <template v-else>
              <button
                class="menububble__button"
                @click="showLinkMenu(getMarkAttrs('link'))"
                :class="{ 'is-active': isActive.link() }"
              >
                <icon name="link" />
              </button>
            </template>

        </div>
      </editor-menu-bubble>
      <editor-floating-menu :editor="editor" v-slot="{ commands, isActive, menu }">
        <div
          class="editor__floating-menu"
          :class="{ 'is-active': menu.isActive }"
          :style="`top: ${menu.top}px`"
        >

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 1 }) }"
            @click="commands.heading({ level: 1 })"
            title="Titre 1"
          >
            H1
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 2 }) }"
            @click="commands.heading({ level: 2 })"
            title="Titre 2"
          >
            H2
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 3 }) }"
            @click="commands.heading({ level: 3 })"
            title="Titre 3"
          >
            H3
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.bullet_list() }"
            @click="commands.bullet_list"
            title="Puces"
          >
            <icon name="ul" />
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.ordered_list() }"
            @click="commands.ordered_list"
            title="Numérotation"
          >
            <icon name="ol" />
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.blockquote() }"
            @click="commands.blockquote"
            title="Bloc"
          >
            <icon name="quote" />
          </button>

          <button
            class="menubar__button"
            :class="{ 'is-active': isActive.code() }"
            @click="commands.code"
            title="Code"
          >
            <icon name="code" />
          </button>

        </div>
      </editor-floating-menu>
      <editor-menu-bar class="fixed" :editor="editor" v-slot="{ commands, isActive }">
        <div class="menubar">
          <div class="toolbar">
            <button
              class="menubar__button"
              @click="commands.undo"
              title="Annuler"
            >
              <icon name="undo" />
            </button>

            <button
              class="menubar__button"
              @click="commands.redo"
              title="Répéter"
            >
              <icon name="redo" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.bold() }"
              @click="commands.bold"
              title="Gras"
            >
              <icon name="bold" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.italic() }"
              @click="commands.italic"
              title="Italic"
            >
              <icon name="italic" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.strike() }"
              @click="commands.strike"
              title="Barrer"
            >
              <icon name="strike" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.underline() }"
              @click="commands.underline"
              title="Souligner"
            >
              <icon name="underline" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.code() }"
              @click="commands.code"
              title="Code"
            >
              <icon name="code" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.paragraph() }"
              @click="commands.paragraph"
              title="Paragraphe"
            >
              <icon name="paragraph" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 1 }) }"
              @click="commands.heading({ level: 1 })"
              title="Titre 1"
            >
              <icon name="h1" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 2 }) }"
              @click="commands.heading({ level: 2 })"
              title="Titre 2"
            >
              <icon name="h2" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 3 }) }"
              @click="commands.heading({ level: 3 })"
              title="Titre 3"
            >
              <icon name="h3" />
            </button>

            <button ref="buttonBack"
              class="menubar__button trigger"
              @click="e => handleClick(e, 2)"
              title="Couleur de surlignage"
            >
              <icon name="background_color" />
            </button>

            <button ref="buttonColor"
              class="menubar__button trigger"
              @click="e => handleClick(e, 1)"
              title="Couleur de police"
            >
              <icon name="text_color" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.bullet_list() }"
              @click="commands.bullet_list"
              title="Puces"
            >
              <icon name="ul" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.ordered_list() }"
              @click="commands.ordered_list"
              title="Numérotation"
            >
              <icon name="ol" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.blockquote() }"
              @click="commands.blockquote"
              title="Bloc"
            >
              <icon name="quote" />
            </button>

            <button
              class="menubar__button"
              :class="{ 'is-active': isActive.todo_list() }"
              @click="commands.todo_list"
              title="Todo"
            >
              <icon name="checklist" />
            </button>

            <button
              class="menubar__button"
              @click="launchUpload(commands.image)"
              title="Image"
            >
              <icon name="image" />
            </button>

            <button
              class="menubar__button"
              @click="toggleSearch"
              :class="{ 'is-active': search }"
              title="Recherche"
            >
              <icon name="search" />
            </button>

            <button
              class="menubar__button"
              @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })"
              title="Tableau"
            >
              <icon name="table" />
            </button>

            <span v-if="isActive.table()">
              <button
                class="menubar__button"
                @click="commands.deleteTable"
                title="Supprimer tableau"
              >
                <icon name="delete_table" />
              </button>
              <button
                class="menubar__button"
                @click="commands.addColumnBefore"
                title="Ajouter une colonne avant"
              >
                <icon name="add_col_before" />
              </button>
              <button
                class="menubar__button"
                @click="commands.addColumnAfter"
                title="Ajouter une colonne après"
              >
                <icon name="add_col_after" />
              </button>
              <button
                class="menubar__button"
                @click="commands.deleteColumn"
                title="Supprimer colonne"
              >
                <icon name="delete_col" />
              </button>
              <button
                class="menubar__button"
                @click="commands.addRowBefore"
                title="Ajouter une ligne avant"
              >
                <icon name="add_row_before" />
              </button>
              <button
                class="menubar__button"
                @click="commands.addRowAfter"
                title="Ajouter une ligne après"
              >
                <icon name="add_row_after" />
              </button>
              <button
                class="menubar__button"
                @click="commands.deleteRow"
                title="Supprimer ligne"
              >
                <icon name="delete_row" />
              </button>
              <button
                class="menubar__button"
                @click="commands.toggleCellMerge"
                title="Fusionner les cellules"
              >
                <icon name="combine_cells" />
              </button>
            </span>
            
            <!-- backgrounds -->
              <div class="backs">
                <editor-menu-bubble class="hidden" :editor="editor" :keep-in-bounds="keepInBounds" v-slot="{ commands, getMarkAttrs }">
                  <div
                    class="menububble"
                    :class="{ 'is-active': coord.isActive==2 }"
                    :style="`left: ${coord.X}px; top: 5px;`"
                  >
                    <button
                      class="menububble__button color bordered"
                      @click="commands.mark({ color: '#FFFFFF' })"
                      style="background: #FFFFFF"
                    >
                      <span v-if="getMarkAttrs('mark').color !== undefined && !colors.includes(getMarkAttrs('mark').color.substring(0, 7))">
                        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation"><path fill="#000000" d="M7.356 10.942a.497.497 0 0 0-.713 0l-.7.701a.501.501 0 0 0-.003.71l3.706 3.707a.501.501 0 0 0 .705.003l7.712-7.712a.493.493 0 0 0-.006-.708l-.7-.7a.504.504 0 0 0-.714 0l-6.286 6.286a.506.506 0 0 1-.713 0l-2.288-2.287z"></path></svg>
                      </span>
                    </button>
        
                    <button v-for="(col,index) in colors" v-bind:key="index"
                      class="menububble__button color"
                      @click="commands.mark({ color: `${col+'CC'}` })"
                      :style="`background: ${col}`"
                    >
                      <span v-if="getMarkAttrs('mark').color==`${col+'CC'}`">
                        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation"><path fill="#fff" d="M7.356 10.942a.497.497 0 0 0-.713 0l-.7.701a.501.501 0 0 0-.003.71l3.706 3.707a.501.501 0 0 0 .705.003l7.712-7.712a.493.493 0 0 0-.006-.708l-.7-.7a.504.504 0 0 0-.714 0l-6.286 6.286a.506.506 0 0 1-.713 0l-2.288-2.287z"></path></svg>
                      </span>
                    </button>
        
                  </div>
                </editor-menu-bubble>
              </div>
              <!-- colors -->
              <div class="colors">
                <editor-menu-bubble class="hidden" :editor="editor" :keep-in-bounds="keepInBounds" v-slot="{ commands, isActive, menu, getMarkAttrs }">
                  <div
                    class="menububble"
                    :class="{ 'is-active': coord.isActive==1 }"
                    :style="`left: ${coord.X}px; top: 5px;`"
                  >
        
                    <button
                      class="menububble__button color"
                      @click="commands.color({ color: '#172B4D' })"
                      style="background: #172B4D"
                    >
                      <span v-if="!colors.includes(getMarkAttrs('color').color)">
                        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation"><path fill="#fff" d="M7.356 10.942a.497.497 0 0 0-.713 0l-.7.701a.501.501 0 0 0-.003.71l3.706 3.707a.501.501 0 0 0 .705.003l7.712-7.712a.493.493 0 0 0-.006-.708l-.7-.7a.504.504 0 0 0-.714 0l-6.286 6.286a.506.506 0 0 1-.713 0l-2.288-2.287z"></path></svg>
                      </span>
                    </button>
        
                    <button v-for="(col,index) in colors" v-bind:key="index"
                      class="menububble__button color"
                      @click="commands.color({ color: col })"
                      :style="`background: ${col}`"
                    >
                      <span v-if="getMarkAttrs('color').color==col">
                        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation"><path fill="#fff" d="M7.356 10.942a.497.497 0 0 0-.713 0l-.7.701a.501.501 0 0 0-.003.71l3.706 3.707a.501.501 0 0 0 .705.003l7.712-7.712a.493.493 0 0 0-.006-.708l-.7-.7a.504.504 0 0 0-.714 0l-6.286 6.286a.506.506 0 0 1-.713 0l-2.288-2.287z"></path></svg>
                      </span>
                    </button>
        
                  </div>
                </editor-menu-bubble>
              </div>
        
              <input class="fileInput" accept="image/*" type="file" ref="fileInput" @change="uploadFile($event)"/>
          </div>
          <div class="participants">
              <div class="participant" v-bind:data-initial="getInitial(p.sessionId, participants)" v-for="(p, index) in participants" v-bind:key="index">
                  <div class="unit">
                      <img v-if="p.image != ''" :src="p.image" v-bind:style="`border-color:${p.initialsToColor}`" />
                      <span v-else class="user-avatar-initials size-30" :style="{ 'background-color': p.initialsToColor }">
                          <span class="initials-letters">
                              {{p.initials}}
                          </span>
                      </span>
                  </div>
              </div>
          </div>
          <div>
              <!--
              <button class="button primary right">
                <div class="menu-wrapper">
                  <a class="fn-menu">
                    <span class="svgicon icon-menu">
                      <icon name="menu" />
                    </span>
                  </a>
                  <ul class="list-unstyled submenu-list float-left">
                    <li>
                      <a @click="toPDF">
                        {{ $t('savePdf') }}
                      </a>
                    </li>
                  </ul>
                </div>
              </button>
              
              <button
                    class="hide-sidebar nf-btn btn-primary"
                    @click="toggle"
                  >
                  <span v-if="fullscreen">Réduire</span>
                  <span v-else>{{ $t('fullScreen') }}</span>
              </button>
              //-->

              <button
                    class="nf-btn btn-primary disable"
                  >
                  <span>{{ $t('save') }}</span>
              </button>
          </div>
        </div>
      </editor-menu-bar>
      <span class="last-save">{{lastSave}}</span>
      <div class="search" v-if="search">
        <input
          ref="search"
          @keydown.enter.prevent="editor.commands.find(searchTerm)"
          placeholder="Recherche …"
          type="text"
          v-model="searchTerm"
        />
        <input
          @keydown.enter.prevent="editor.commands.replace(replaceWith)"
          placeholder="Remplacer …"
          type="text"
          v-model="replaceWith"
        />
        <button class="button" @click="editor.commands.find(searchTerm)">
          Chercher
        </button>
        <button class="button" @click="editor.commands.clearSearch()">
          Effacer
        </button>
        <button class="button" @click="editor.commands.replace(replaceWith)">
          Remplacer
        </button>
        <button class="button" @click="editor.commands.replaceAll(replaceWith)">
          Remplacer partout
        </button>
      </div>
      <editor-content class="editor__content editor-content" :editor="editor"  />
    </template>
    <em v-else>
      {{message}}
    </em>
  </div>
</div>
</template>

<script>
import io from 'socket.io-client'
import Icon from './icon.vue'
import { Editor, EditorContent, EditorMenuBar, EditorFloatingMenu, EditorMenuBubble } from 'tiptap'
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Table,
  TableHeader,
  TableCell,
  TableRow,
  Strike,
  Underline,
  History,
  Collaboration,
  Search,
  Image
} from 'tiptap-extensions'
import Cursor from './Cursor'
import HighlightMark from './HighlightMark'
import ColorMark from './ColorMark'

import jsPDF from 'jspdf'
import htmlDocx from 'html-docx-js/dist/html-docx'
import { saveAs } from 'file-saver';

export default {
  components: {
    EditorContent,
    EditorMenuBar,
    Icon,
    EditorFloatingMenu,
    EditorMenuBubble
  },
  data() {
    return {
      searchTerm: null,
      replaceWith: null,
      keepInBounds: true,
      fullscreen: false,
      search: false,
      loading: true,
      editor: null,
      socket: null,
      count: 0,
      message: 'Connecting to server …',
      linkUrl: null,
      linkMenuIsActive: false,
      participants: [],
      coord: {X: 0, Y: 0, isActive: 0},
      sessionId: localStorage.getItem('userId'),
      name: localStorage.getItem('name'),
      initials: localStorage.getItem('initials'),
      initialsToColor: localStorage.getItem('initialsToColor'),
      docName: null,
      image: localStorage.getItem('image'),
      colors: ['#97A0AF', '#6554C0', '#00B8D9', '#36B37E', '#FF5630', '#FF991F'],
      command: null,
      lastSave: ''
    }
  },
  methods: {
    onInit({ doc, version, name }) {
      this.loading = false
      if (this.editor) {
        this.editor.destroy()
      }
      this.docName = name
      this.editor = new Editor({
        content: doc,
        extensions: [
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Link(),
          new Bold(),
          new Code(),
          new Italic(),
          new Strike(),
          new Underline(),
          new History(),
          new Image(),
          new Table({
            resizable: true,
          }),
          new TableHeader(),
          new TableCell(),
          new TableRow(),
          new Search({
            disableRegex: false,
          }),
          new HighlightMark(),
          new ColorMark(),
          new Collaboration({
            // the initial version we start with
            // version is an integer which is incremented with every change
            version,
            // debounce changes so we can save some requests
            debounce: 250,
            // onSendable is called whenever there are changed we have to send to our server
            onSendable: ({ sendable }) => {
              this.socket.emit('update', sendable)
            },
            clientID: this.sessionId,
          }),
          new Cursor({
            clientID: this.sessionId,
            name: this.name,
            image: this.image,
            initials: this.initials,
            initialsToColor: this.initialsToColor,
            onSendable: ({ sendable }) => {
              this.socket.emit('cursor', sendable)
            },
            debounce: 50
          })
        ],
      })
    },
    toggle() {
      this.fullscreen = !this.fullscreen
    },
    toggleSearch() {
      this.search = !this.search
    },
    handleClick(e, type){
      this.coord.X = e.clientX
      this.coord.Y = e.clientY
      this.coord.isActive = type
      console.log(this.coord.isActive);
      console.log(this.coord.X);
      console.log(this.coord.Y);
    },

    close (e) {
      if (this.coord.isActive!=0 && this.$refs.buttonColor != e.target && this.$refs.buttonBack != e.target) {
        this.coord.isActive = 0
      }
    },

    setCount(count) {
      this.count = count
    },

    launchUpload(command){
      this.command = command
      this.$refs.fileInput.click()
      this.uploadFile(this.$refs.fileInput.files[0])
    },

    toPDF(){
      /*var xhr = new XMLHttpRequest();
      var url = "https://illisite.netframe.info/media/download/160"
      window.console.log(url)
      var xhr = new XMLHttpRequest();
      xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
          console.log(reader.result);
        }
        reader.readAsDataURL(xhr.response);
      };
      xhr.open('GET', url);
      xhr.responseType = 'blob';*/
      var doc = new jsPDF('p', 'mm', 'a4')

      doc.fromHTML(this.editor.getHTML(), 15, 15)
      doc.save(this.docName+'.pdf')
    },

    savePDF(dataURL){
      window.console.log(dataURL)
    },

    convertImgToBase64URL(url, callback, outputFormat){
      var img = new Image();
      img.crossOrigin = 'Anonymous';
      img.onload = function(){
          var canvas = document.createElement('CANVAS'),
          ctx = canvas.getContext('2d'), dataURL;
          canvas.height = img.height;
          canvas.width = img.width;
          ctx.drawImage(img, 0, 0);
          dataURL = canvas.toDataURL(outputFormat);
          callback(dataURL);
          canvas = null;
      };
      img.src = url;
    },

    toDOCX(){
      var converted = htmlDocx.asBlob(this.editor.getHTML());
      saveAs(converted, this.docName+'.docx');
    },

    /*uploadFile(file) {
      let reader = new FileReader
      return new Promise((accept, fail) => {
        reader.onload = () => accept(reader.result)
        reader.onerror = () => fail(reader.error)
        // Some extra delay to make the asynchronicity visible
        setTimeout(() => reader.readAsDataURL(file), 1500)
      })
    },*/
    /*launchUpload(){
      this.$refs.fileInput.click()
      //this.uploadFile(this.$refs.fileInput.files[0]).then(file => {
      //  window.console.log(file)
      //})
    },*/

    uploadFile(e) {
      /*let reader = new FileReader
      reader.onload = () => this.socket.emit('image', reader.result)
      reader.readAsDataURL(e.target.files[0])*/

      const URL = '/collab/upload-file';

      let data = new FormData();
      //data.append('name', 'my-picture');
      if(e && e.target.files.length){
        data.append('file', e.target.files[0]);

        let config = {
          header : {
            'Content-Type' : 'image/*'
          }
        }

        axios.post(URL, data, config).then(response => {
            this.command({ src: response.data.path });
          }
        )
      }
    },

    getInitial(sessionId, participants) {
      const participant = participants.find(p => p.sessionId == sessionId);
      return participant ? participant.initials : 'X';
    },

    showLinkMenu(attrs) {
      this.linkUrl = attrs.href
      this.linkMenuIsActive = true
      this.$nextTick(() => {
        this.$refs.linkInput.focus()
      })
    },
    hideLinkMenu() {
      this.linkUrl = null
      this.linkMenuIsActive = false
    },
    setLinkUrl(command, url) {
      command({ href: url })
      this.hideLinkMenu()
    },
    showImagePrompt(command) {
      const src = prompt('Enter the url of your image here')
      if (src !== null) {
        command({ src })
      }
    },
  },
  mounted() {
    document.addEventListener('click', this.close)
    // server implementation: https://glitch.com/edit/#!/tiptap-sockets
    
    //this.socket = io(window.location.hostname+':3000', {query: 'docId='+this.$route.params.id+'&userId='+this.sessionId})
    // K8S PATH : /collab/broadcast/socket.io
    
    if (document.head.querySelector("[name~=collab-ws-url][content]").content != '') {
        var collabUrl = document.head.querySelector("[name~=collab-ws-url][content]").content;
    } else {
        var collabUrl = window.location.origin;
    }
    
    if (document.head.querySelector("[name~=collab-ws-port][content]").content != '') {
        collabUrl += ':' + document.head.querySelector("[name~=collab-ws-port][content]").content;
    }
    
    this.socket = io(collabUrl, {
      path: document.head.querySelector("[name~=collab-ws-path][content]").content,
      query: {
        docId: this.$route.params.id,
        userId: this.sessionId
      }
    })
      // get the current document and its version
      .on('init', data => this.onInit(data))
      // send all updates to the collaboration extension
      .on('update', data => {
        var d = new Date(),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear(),
        hour = d.getHours(),
        min = d.getMinutes();

        month = (month < 10) ? '0' + month : month;
        day = (day < 10) ? '0' + day : day;
        hour = (hour < 10) ? '0' + hour : hour;
        min = (min < 10) ? '0' + min : min;
        this.lastSave = [day, month, year].join('/')+' à ' + hour + ':' + min;
        this.editor.extensions.options.collaboration.update(data)
      })
      // get count of connected users
      // .on('getCount', count => this.setCount(count))
      .on('message', data => this.message = data.message)
      .on('cursor', data => {
        if(data.sessionId !=  this.sessionId && !this.participants.find(p => p.sessionId == data.sessionId))
          this.participants.push({
            sessionId: data.sessionId, 
            image: data.image, 
            name: data.name, 
            initials: data.initials, 
            initialsToColor: data.initialsToColor
          })
        this.editor.extensions.options.cursor.update(data)
      })
  },
  beforeDestroy() {
    document.removeEventListener('click', this.close)
    this.editor.destroy()
    this.socket.destroy()
  },
}
</script>
<style lang="scss">
@import "./variables";
.disable:disabled,
.disable[disabled]{
  opacity: .5
}
.colors .hidden, .backs .hidden{
  background: #fff;
  background: rgb(255, 255, 255) none repeat scroll 0% 0%;
  border-radius: 3px;
  box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.31) 0px 0px 1px;
  box-sizing: border-box;
  overflow: auto;
  padding: 4px 10px;
  max-height: 90vh;
}
button.bordered{
  border: 1px solid #ccc!important
}
.colors button, .backs button{
  width: 28px;
  height: 28px;
  margin: 2px;
  padding: 0;
  border: 0;
  border-radius: 5px;
  cursor: pointer
}
.editor {
  height: -webkit-fill-available;
  position: relative;
  &__floating-menu {
    position: absolute;
    z-index: 1;
    margin-top: -0.25rem;
    visibility: hidden;
    opacity: 0;
    // transition: opacity 0.2s, visibility 0.2s;
    &.is-active {
      opacity: 1;
      visibility: visible;
    }
  }
}
l[data-type="todo_list"] {
  padding-left: 0;
}
li[data-type="todo_item"] {
  display: flex;
  flex-direction: row;
}
.last-save {
    display: block;
    text-align:right;
    font-size: 0.8rem;
    height: 1.5rem;
}
.todo-checkbox {
  border: 2px solid $primary;
  height: 0.9em;
  width: 0.9em;
  box-sizing: border-box;
  margin-right: 10px;
  margin-top: 0.3rem;
  user-select: none;
  -webkit-user-select: none;
  cursor: pointer;
  border-radius: 0.2em;
  background-color: transparent;
  transition: 0.4s background;
}
.todo-content {
  flex: 1;
  > p:last-of-type {
    margin-bottom: 0;
  }
  > ul[data-type="todo_list"] {
    margin: .5rem 0;
  }
}
li[data-done="true"] {
  > .todo-content {
    > p {
      text-decoration: line-through;
    }
  }
  > .todo-checkbox {
    background-color: $primary;
  }
}
li[data-done="false"] {
  text-decoration: none;
}
.search {
  display: flex;
  flex-wrap: wrap;
  background-color: rgba($primary, 0.1);
  padding: 0.5rem;
  border-radius: 5px;
  margin: 1rem 0;
  input {
    padding: 0.25rem;
    border: 0;
    border-radius: 3px;
    margin-right: 0.2rem;
    font: inherit;
    font-size: 0.8rem;
    width: 20%;
    flex: 1;
  }
  button {
  }
}

//others
.fileInput{
  opacity: 0;
  visibility: hidden;
  display:none;
}
.colors, .backs{
  position: relative!important
}
.colors .hidden{
  background: #fff
}
.menububble.is-active{
  opacity: 1;
  visibility: visible;
}
.menububble {
  position: absolute;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  z-index: 20;
  background: #000;
  border-radius: 5px;
  opacity: 0;
  visibility: hidden;
  padding: .3rem;
  margin-bottom: .5rem;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  -webkit-transition: opacity .2s,visibility .2s;
  transition: opacity .2s,visibility .2s;
}
.telepointer {
  position: relative;
  transition: opacity 200ms ease 0s;
}
.telepointer.telepointer-dim {
  opacity: 0.2;
}
.telepointer.telepointer-selection-badge{
  border-left: 1px solid #000;
  border-right: 1px solid #000;
  margin-right: -2px;
}
 .telepointer.telepointer-selection-badge::after {
  content: attr(data-initial);
  position: absolute;
  display: block;
  top: -14px;
  font-size: 9px;
  color: black;
  font-weight: bold;
  left: -1px;
  line-height: initial;
  padding: 2px;
  border-radius: 2px 2px 2px 0px;
  color: #fff;
  background-color:inherit;
  white-space: nowrap;
}



//add color after
/*.telepointer::after {
    background-color: red!important;
    color: rgb(255, 255, 255);
    border-color: rgb(64, 50, 148);
}*/

.add button{
  border-radius: 100%;
  cursor: pointer;
  box-shadow: none
}
.participants{
  float: right;
  min-height: 40px
}
.participant{
  position: relative;
  width: 30px;
  height: 30px;
  float: left
}
.participant .unit{
  width: 30px;
  height: 30px;
  border-radius: 100%;
}

.participant .unit img{
  max-width: 100%;
  max-width: 100%;
  border-radius: 100%;
  border: 2px solid #fff;
}

/*.participant::before {
  content: "";
  display: block;
  position: absolute;
  right: -1px;
  bottom: -1px;
  width: 13px;
  height: 13px;
  z-index: 2;
  border-radius: 3px;
  background: rgb(64, 50, 148) none repeat scroll 0% 0%;
  color: rgb(255, 255, 255);
  font-size: 9px;
  line-height: 0;
  padding-top: 7px;
  text-align: center;
  box-shadow: rgb(255, 255, 255) 0px 0px 1px;
  box-sizing: border-box;
}*/
</style>
