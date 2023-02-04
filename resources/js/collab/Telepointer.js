import { Plugin, PluginKey } from 'tiptap'
import { Decoration, DecorationSet } from 'prosemirror-view'
import { Selection } from 'prosemirror-state';

function getDecorations({ doc }) {
  return DecorationSet.create(doc, [])
}

// function style(options) {
//   const color = (options && options.color) || 'black';
//   return `border-left: 1px solid ${color}; border-right: 1px solid ${color}; margin-right: -2px;`;
// }

const createTelepointers = (
  from,
  to,
  sessionId,
  isSelection,
  initial,
  color,
  initialsToColor
) => {
  let decorations = [];
  // const avatarColor = getAvatarColor(sessionId);
  // const color = "red";//avatarColor.index.toString();
  if (isSelection) {
    const className = `telepointer color-${color} telepointer-selection`;
    const style = `background-color:${initialsToColor};border-color: ${initialsToColor}!important`;
    decorations.push(
      (Decoration).inline(
        from,
        to,
        {
            class: className, 
            'data-initial': initial,
            'style': style,
        },
        {
            pointer: { sessionId }
        },
      ),
    );
  }

  const cursor = document.createElement('span');
  cursor.textContent = '\u200b';
  cursor.className = `telepointer color-${color} telepointer-selection-badge`;
  cursor.setAttribute('style', `background-color:${initialsToColor};border-color: ${initialsToColor}!important`);
  // cursor.style.cssText = `${style({ color/*avatarColor.color.solid*/ })};`;
  cursor.setAttribute('data-initial', initial);
  decorations = decorations.concat(
    (Decoration).widget(to, cursor, { pointer: { sessionId } }),
  );
  return decorations
};

const findPointers = (id, decorations=[]) =>
  decorations
    .find()
    .reduce(
      (arr, deco) =>
        deco.spec.pointer.sessionId === id ? arr.concat(deco) : arr,
      [],
    );

const getValidPos = (tr, pos) => {
  const resolvedPos = tr.doc.resolve(pos);
  const validSelection = Selection.findFrom(resolvedPos, -1, true);
  return validSelection ? validSelection.$anchor.pos : pos;
};

const getInitial = (sessionId, participants) => {
  const participant = participants.find(p => p.sessionId == sessionId);
  console.log(participant);
  return participant ? participant.initials : 'X';
}

export default function TelepointerPlugin({participants}) {
  // let participants = []
  const pluginKey = new PluginKey('telepointer')
  const plugin = new Plugin({
    key: pluginKey,
    state: {
      init: (_, { doc }) => getDecorations({ doc, name }),
      // init: () => [],
      apply: (transaction, decorationSet/*, oldState, state*/) => {
        const telepointerData = transaction.getMeta("telepointer");
        // window.console.log(telepointerData)
        let remove = []
        if(telepointerData){
          const { anchor, head, sessionId } = telepointerData;
          
          const oldPointers = findPointers(
            sessionId,
            decorationSet,
          );
          
          if(oldPointers)
            remove = remove.concat(oldPointers)

          const rawFrom = anchor < head ? anchor : head;
          const rawTo = anchor >= head ? anchor : head;
          const isSelection = rawTo - rawFrom > 0;
          let from = 1;
          let to = 1;
        
          try {
            from = getValidPos(
              transaction,
              isSelection ? Math.max(rawFrom - 1, 0) : rawFrom,
              );
              to = isSelection ? getValidPos(transaction, rawTo) : from;
        
            } catch (err) {err}
          if(remove.length)
            decorationSet = decorationSet.remove(remove)
          let add = []
          // user Initial: this.getInitial(sessionId),
          const index = participants.findIndex(p => p.sessionId == sessionId);
          let initialsToColor = '#fff';
          for (let key in participants) {
            console.log(key, participants[key]);
            console.log(participants[key].sessionId);
            console.log(sessionId);
            if (participants[key].sessionId == sessionId) {
                initialsToColor = participants[key].initialsToColor;
            }
            console.log(initialsToColor);
          }
          
          console.log(index);
          console.log(participants);
          add = add.concat(createTelepointers(from, to, sessionId, isSelection, getInitial(sessionId, participants), index, initialsToColor));
          return decorationSet.add(transaction.doc, add)
        }
        return decorationSet
      },
    },
    props: {
      decorations: state => {
        return plugin.getState(state)
      },
    }
  })
  return plugin
}

export function SelectPlugin() {
  return new Plugin({
    view () {
      return {
        update: function (view, prevState) {
          var state = view.state;
          
          if (prevState && prevState.doc.eq(state.doc) &&
            prevState.selection.eq(state.selection))
            return;      
          const { state: { tr } } = view
          view.dispatch(tr.setMeta('cursor.select', true))
        }
      }
    }
  })
}