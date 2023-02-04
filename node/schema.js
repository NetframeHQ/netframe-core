const Schema =  require('prosemirror-model').Schema

const schema = {
  "nodes": {
    "doc": {
      "content": "block+"
    },
    "text": {
      "group": "inline"
    },
    "paragraph": {
      "content": "inline*",
      "group": "block",
      "draggable": false,
      "parseDOM": [
        {
          "tag": "p"
        }
      ]
    },
    "hard_break": {
      "inline": true,
      "group": "inline",
      "selectable": false,
      "parseDOM": [
        {
          "tag": "br"
        }
      ]
    },
    "bullet_list": {
      "group": 'block',
      "content": 'list_item+',
      "parseDOM": [
        {
          "tag": 'ul'
        }
      ]
    },
    "ordered_list": {
      "group": 'block',
      "content": 'list_item+',
      "attrs": { "order": { "default": 1 } },
      "parseDOM": [
        {
          "tag": 'ol',
        }
      ]
    },
    "list_item": {
      "content": 'paragraph block*',
      "defining": true,
      "parseDOM": [
        {
          "tag": 'li'
        }
      ]
    },
    "todo_item": {
      "group": 'block',
      "content": 'paragraph+',
      "attrs": {
        "done": { "default": false },
      },
      "parseDOM": [
        {
          "priority": 51,
          "tag": '[data-type="todo_item"]',
        }
      ]
    },
    "todo_list": {
      "group": 'block',
      "content": 'todo_item+',
      "toDOM": () => ['ul', { 'data-type': "todo_list" }, 0],
      "parseDOM": [
        {
          "tag": '[data-type="todo_list"]',
        }
      ]
    },
    "table": {
      "group": 'block',
      "content": 'table_row+',
      "parseDOM": [
        {
          "tag": 'table',
        }
      ]
    },
    "table_row": {
      "group": 'block',
      "content": 'table_cell+',
      "parseDOM": [
        {
          "tag": 'tr',
        }
      ]
    },
    "table_cell": {
      "content": 'paragraph block*',
      "defining": true,
      "parseDOM": [
        {
          "tag": 'td'
        }
      ]
    },
    "blockquote": {
      "content": 'block+',
      "draggable": false,
      "defining": true,
      "group": "block",
      "selectable": false,
      "parseDOM": [
        {
          "tag": "blockquote"
        }
      ]
    },
    "heading": {
      "attrs": {
        "level": {
          "default": 1
        }
      },
      "content": "inline*",
      "group": "block",
      "defining": true,
      "draggable": false,
      "parseDOM": [
        {
          "tag": "h1",
          "attrs": {
            "level": 1
          }
        },
        {
          "tag": "h2",
          "attrs": {
            "level": 2
          }
        },
        {
          "tag": "h3",
          "attrs": {
            "level": 3
          }
        }
      ]
    },
    "image": {
      "inline": true,
      "group": "inline",
      "draggable": true,
      "selectable": false,
      "attrs": {
        "src": "",
        "alt": { "default": null },
        "title": { "default": null }
      },
      "parseDOM": [
        {
          "tag": "img",
        }
      ],
    }
  },
  "marks": {
    "bold": {
      "parseDOM": [
        {
          "tag": "strong"
        },
        {
          "tag": "b"
        },
        {
          "style": "font-weight"
        }
      ]
    },
    "code": {
      "parseDOM": [
        {
          "tag": "code"
        }
      ]
    },
    "link": {
      "attrs": {"href": {}},
      "parseDOM": [
        {
          "tag": "a"
        }
      ]
    },
    "italic": {
      "parseDOM": [
        {
          "tag": "i"
        },
        {
          "tag": "em"
        },
        {
          "style": "font-style=italic"
        }
      ]
    },
    "strike": {
      "parseDOM": [
        {
          "tag": "strike"
        },
      ]
    },
    "underline": {
      "parseDOM": [
        {
          "tag": "u"
        }
      ]
    },
    "mark": {
      "attrs": {
        "color": { "default": "#fff"}
      },
      "parseDOM": [
        {
          "tag": "span"
        }
      ]
    },
    "color": {
      "attrs": {
        "color": { "default": "#000"}
      },
      "parseDOM": [
        {
          "tag": "span"
        }
      ]
    },
  }
}
module.exports = new Schema(schema)
// export default new Schema(schema)