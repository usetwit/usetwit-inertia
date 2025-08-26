import {Mark} from '@tiptap/core'

export default Mark.create({
    name: 'fontWeight',

    addOptions() {
        return {
            weights: [
                'thin', 'extralight', 'light', 'normal', 'medium',
                'semibold', 'bold', 'extrabold', 'black'
            ],
            HTMLAttributes: {},
        }
    },

    parseHTML() {
        return this.options.weights.map(weight => ({
            tag: `span.font-${weight}`,
        }))
    },

    renderHTML({mark, HTMLAttributes}) {
        return [
            'span',
            {
                ...HTMLAttributes,
                class: mark.attrs.weight,
            },
            0
        ]
    },

    addAttributes() {
        return {
            weight: {
                default: 'font-normal',
                parseHTML: element => {
                    const match = element.className.match(/font-(thin|extralight|light|normal|medium|semibold|bold|extrabold|black)/)
                    return match ? `font-${match[1]}` : 'font-normal'
                },
                renderHTML: attributes => {
                    if (!attributes.weight) return {}
                    return {class: attributes.weight}
                },
            }
        }
    },

    addCommands() {
        return {
            toggleFontWeight: (weight) => ({commands}) => {
                return commands.toggleMark(this.name, {weight})
            },
        }
    },
})
