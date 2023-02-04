import { Mark } from 'tiptap'
import { updateMark } from 'tiptap-commands'

export default class HighlightMark extends Mark {
  get name() {
    return "mark";
  }

  get schema() {
    return {
      attrs: {
        color: {
          default: "#C5E1B5AA"
        }
      },
      parseDOM: [
        {
          tag: "span"
        }
      ],
      toDOM: mark => [
        "span",
        {
          style: `background:${mark.attrs.color}`
        },
        0
      ]
    };
  }

  commands({ type }) {
    return attrs => updateMark(type, attrs);
  }

}