import { Mark } from 'tiptap'
import { updateMark } from 'tiptap-commands'

export default class ColorMark extends Mark {
  get name() {
    return "color";
  }

  get schema() {
    return {
      attrs: {
        color: {
          default: "#C5E1B5"
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
          style: `color:${mark.attrs.color}`
        },
        0
      ]
    };
  }

  commands({ type }) {
    return attrs => updateMark(type, attrs);
  }

}