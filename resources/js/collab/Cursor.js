import { Extension } from 'tiptap'
import TelepointerPlugin, {SelectPlugin} from './Telepointer'

const findParticipant = (id, participants=[]) =>
  participants.find(p => p.sessionId == id);

export default class Cursor extends Extension {

  get name() {
    return 'cursor'
  }

  init() {
    this.getSendableCursor = this.debounce(state => {
      const {selection: {anchor, head}} = state
      const sendable = {
        anchor, 
        head, 
        sessionId: this.options.clientID, 
        image: this.options.image, 
        name: this.options.name, 
        initials: this.options.initials, 
        initialsToColor: this.options.initialsToColor
      }
      
      if (sendable) {
        this.options.onSendable({
          editor: this.editor,
          sendable,
        })
      }
    }, this.options.debounce)

    this.participants = []

    this.editor.on('transaction', (val) => {
      // window.console.log(JSON.stringify(val))
      if(val.transaction.getMeta("cursor.select"))
        this.getSendableCursor(val.state)
    })
  }

  get defaultOptions() {
    return {
      clientID: Math.floor(Math.random() * 0xFFFFFFFF),
      name: "",
      image: "",
      initials: "",
      initialsToColor: "",
      onSendable: () => {},
      debounce: 250,
      update: (data) => {
        const { view } = this.editor
        const { state: { tr } } = view
        if(data.sessionId !== this.options.clientID){
          if(!findParticipant(data.sessionId, this.participants))
          console.log(data);
            this.participants.push({
                sessionId: data.sessionId, 
                name: data.name, 
                image: data.image, 
                initials: data.initials, 
                initialsToColor: data.initialsToColor
            })

          view.dispatch(tr.setMeta('telepointer', data)) 
        }
      },
    } 
  }

  get plugins() {
    return [
      TelepointerPlugin({participants: this.participants}),
      SelectPlugin({}),
    ]
  }

  debounce(fn, delay) {
    let timeout
    return function (...args) {
      if (timeout) {
        clearTimeout(timeout)
      }
      timeout = setTimeout(() => {
        fn(...args)
        timeout = null
      }, delay)
    }
  }

}