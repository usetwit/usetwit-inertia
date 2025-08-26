<script setup>
import {ref, computed} from 'vue'
import {useEditor, EditorContent} from '@tiptap/vue-3'
import Textarea from "@/components/Form/Textarea.vue";
import Document from '@tiptap/extension-document'
import Text from '@tiptap/extension-text'
import ListItem from '@tiptap/extension-list-item'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import Underline from '@tiptap/extension-underline'
import Strike from '@tiptap/extension-strike'
import Blockquote from '@tiptap/extension-blockquote'
import Code from '@tiptap/extension-code'
import CodeBlock from '@tiptap/extension-code-block'
import Paragraph from '@tiptap/extension-paragraph'
import Heading from '@tiptap/extension-heading'
import BulletList from '@tiptap/extension-bullet-list'
import OrderedList from '@tiptap/extension-ordered-list'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableHeader from '@tiptap/extension-table-header'
import TableCell from '@tiptap/extension-table-cell'
import HardBreak from '@tiptap/extension-hard-break'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import History from '@tiptap/extension-history'
import FontWeightButton from "@/components/Form/WysiwygButtons/FontWeightButton.vue";
import FontWeight from "@/components/Form/TipTap/FontWeight.js";

const props = defineProps({
    disabled: {type: Boolean, default: false},
    invalid: {type: Boolean, default: false}
})

const model = defineModel({type: String, required: true})
const isHtmlMode = ref(false)
const htmlContent = ref('')

const editor = useEditor({
    content: model.value,
    editable: !props.disabled,
    extensions: [
        Document, Text, Paragraph, Heading.configure({levels: [1, 2, 3]}),
        Bold, Italic, Underline, Strike, Blockquote, Code, CodeBlock,
        BulletList, OrderedList,
        ListItem,
        Link.configure({openOnClick: true}),
        Image,
        Table.configure({resizable: true}),
        TableRow,
        TableHeader,
        TableCell,
        HardBreak,
        HorizontalRule,
        History,
        FontWeight,
    ],
    editorProps: {
        attributes: {
            class: 'prose focus:outline-none w-full max-w-none',
        },
    },
    onUpdate: ({editor}) => {
        model.value = editor.getHTML()
    }
})

const toggleHtmlMode = () => {
    if (isHtmlMode.value) {
        editor.value.commands.setContent(htmlContent.value, true)
    } else {
        htmlContent.value = editor.value.getHTML()
    }
    isHtmlMode.value = !isHtmlMode.value
}

const updateEditor = () => {
    editor.value.commands.setContent(htmlContent.value, true)
}

const toolbarGroups = computed(() => editor.value ? [
    {
        label: 'Text Formatting',
        buttons: [
            { label: 'B', action: () => editor.value.chain().focus().toggleBold().run(), active: () => editor.value.isActive('bold') },
            { label: 'I', action: () => editor.value.chain().focus().toggleItalic().run(), active: () => editor.value.isActive('italic') },
            { label: 'U', action: () => editor.value.chain().focus().toggleUnderline().run(), active: () => editor.value.isActive('underline') },
            { label: 'S', action: () => editor.value.chain().focus().toggleStrike().run(), active: () => editor.value.isActive('strike') }
        ]
    },
    {
        label: 'Lists & Blocks',
        buttons: [
            { label: '• List', action: () => editor.value.chain().focus().toggleBulletList().run(), active: () => editor.value.isActive('bulletList') },
            { label: '1. List', action: () => editor.value.chain().focus().toggleOrderedList().run(), active: () => editor.value.isActive('orderedList') },
            { label: '↵ Split', action: () => editor.value.chain().focus().splitListItem('listItem').run(), active: () => false, disabled: () => !editor.value.can().splitListItem('listItem') },
            { label: '⮋ Sink', action: () => editor.value.chain().focus().sinkListItem('listItem').run(), active: () => false, disabled: () => !editor.value.can().sinkListItem('listItem') },
            { label: '⮉ Lift', action: () => editor.value.chain().focus().liftListItem('listItem').run(), active: () => false, disabled: () => !editor.value.can().liftListItem('listItem') }
        ]
    },
    {
        label: 'Code & Quotes',
        buttons: [
            { label: '{ }', action: () => editor.value.chain().focus().toggleCodeBlock().run(), active: () => editor.value.isActive('codeBlock') },
            { label: '“”', action: () => editor.value.chain().focus().toggleBlockquote().run(), active: () => editor.value.isActive('blockquote') }
        ]
    },
    {
        label: 'Media & Tables',
        buttons: [
            { label: 'Image', action: () => editor.value.chain().focus().setImage({ src: prompt('Enter image URL') }).run(), active: () => editor.value.isActive('image') },
            { label: 'Table', action: () => editor.value.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(), active: () => editor.value.isActive('table') }
        ]
    }
] : [])
</script>

<template>
    <div v-if="editor" :class="['border rounded-lg p-2', { 'border-red-500': invalid, 'opacity-50 pointer-events-none': disabled }]">
        <div class="flex flex-wrap gap-4 mb-2 border-b pb-2">
            <div v-for="group in toolbarGroups" :key="group.label" class="flex gap-2">
                <button v-for="btn in group.buttons" :key="btn.label" type="button" @click="btn.action"
                        :class="['p-1 rounded-md text-sm', { 'bg-blue-500 text-white': btn.active() }]">
                    {{ btn.label }}
                </button>
            </div>
            <FontWeightButton :editor="editor" />
            <button type="button" @click="toggleHtmlMode" class="p-1 rounded-md text-sm bg-gray-300">
                {{ isHtmlMode ? "WYSIWYG" : "&lt;HTML&gt;" }}
            </button>
        </div>

        <editor-content v-if="!isHtmlMode"
                        :editor="editor"
                        class="min-h-[150px] p-2 border rounded-md"
        />
        <Textarea v-else
                  v-model="htmlContent"
                  @input="updateEditor"
                  class="w-full min-h-[150px] p-2 border rounded-md"
        />
    </div>
</template>
