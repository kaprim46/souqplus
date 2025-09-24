<template>
    <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-gray-700" v-if="editor">
        <div class="flex align-middle gap-x-0.5 border-b border-gray-200 p-2 dark:border-gray-700">
            <button
                @click="editor.chain().focus().toggleBold().run()" :disabled="!editor.can().chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-bold>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 12a4 4 0 0 0 0-8H6v8"/><path d="M15 20a4 4 0 0 0 0-8H6v8Z"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleItalic().run()" :disabled="!editor.can().chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-italic>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleStrike().run()" :disabled="!editor.can().chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-strike>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" x2="20" y1="12" y2="12"/></svg>
            </button>
            <button
                @click="modalOpen = true; whichAction = 'link'"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-link>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleOrderedList().run()" :disabled="!editor.can().chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-ol>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleBulletList().run()" :disabled="!editor.can().chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-ul>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleBlockquote().run()" :disabled="!editor.can().chain().focus().toggleBlockquote().run()" :class="{ 'is-active': editor.isActive('blockquote') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-blockquote>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 6H3"/><path d="M21 12H8"/><path d="M21 18H8"/><path d="M3 12v6"/></svg>
            </button>
            <button
                @click="editor.chain().focus().toggleUnderline().run()" :disabled="!editor.can().chain().focus().toggleUnderline().run()" :class="{ 'is-active': editor.isActive('underline') }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-code>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 16 4-4-4-4"/><path d="m6 8-4 4 4 4"/><path d="m14.5 4-5 16"/></svg>
            </button>
            <!-- Set Image -->
            <button
                @click="modalOpen = true; whichAction = 'image'"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-image>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </button>
            <!-- Heading -->
            <button
                @click="onSetHeading(1)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 1 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h1>
                H1
            </button>
            <button
                @click="onSetHeading(2)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 2 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h2>
                H2
            </button>
            <button
                @click="onSetHeading(3)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 3 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h3>
                H3
            </button>
            <button
                @click="onSetHeading(4)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 4 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 4 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h4>
                H4
            </button>
            <button
                @click="onSetHeading(5)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 5 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 5 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h5>
                H5
            </button>
            <button
                @click="onSetHeading(6)" :disabled="!editor.can().chain().focus().toggleHeading({ level: 6 }).run()" :class="{ 'is-active': editor.isActive({ heading: { level: 6 } }) }"
                class="w-8 h-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" type="button" data-hs-editor-h6>
                H6
            </button>
        </div>

        <editor-content
            :editor="editor"
            :placeholder="$t('Enter text here...')"
        />

        {{text}}
    </div>

    <!-- Modal -->
    <v-modal :is-open="modalOpen" @update:closeModal="modalOpen = false">
        <template #content>
            <v-input
                type="url"
                id="link"
                name="link"
                required
                aria-describedby="link-error"
                :placeholder="$t('Link')"
                @update:input="editor.chain().focus().extendMarkRange('link').setLink({ href: $event }).run()"
                v-if="whichAction === 'link'"
            />
            <v-input
                type="url"
                id="image"
                name="image"
                :placeholder="$t('Image URL')"
                required
                @update:input="editor.chain().focus().setImage({ src: $event }).run()"
                v-if="whichAction === 'image'"
            />
        </template>
        <template #footer>
            <v-button color="blue" @click="modalOpen = false">
                {{ $t('Save') }}
            </v-button>
        </template>
    </v-modal>
</template>

<script setup>
import {ref} from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from '@tiptap/starter-kit'
import {Link} from '@tiptap/extension-link'
import {Underline} from "@tiptap/extension-underline";
import {BulletList} from "@tiptap/extension-bullet-list";
import {OrderedList} from "@tiptap/extension-ordered-list";
import {Paragraph} from "@tiptap/extension-paragraph";
import {ListItem} from "@tiptap/extension-list-item";
import {Blockquote} from "@tiptap/extension-blockquote";
import {Placeholder} from "@tiptap/extension-placeholder";
import {Heading} from "@tiptap/extension-heading";
import {Image} from "@tiptap/extension-image";

const props = defineProps({
    value:{
      type: [String, null, undefined],
      default: ''
    },
    placeholder: {
        type: String,
        default: 'Enter text here...',
    },
});

const text          = ref('');
const modalOpen     = ref(false);
const whichAction   = ref('link');
const emit          = defineEmits(['update:input']);

const editor = useEditor({
    onUpdate({ editor }) {
        emit('update:input', editor.getHTML())
    },
    content: props.value,
    extensions: [
        StarterKit,
        Underline,
        ListItem,
        Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: 'v-nazih-image',
            }
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
            emptyNodeClass: 'text-gray-600 dark:text-gray-400'
        }),
        Paragraph.configure({
            HTMLAttributes: {
                class: 'v-nazih-paragraph',
            }
        }),
        Link.configure({
            HTMLAttributes: {
                class: 'v-nazih-link',
            }
        }),
        BulletList.configure({
            HTMLAttributes: {
                class: 'v-nazih-bullet-list',
            }
        }),
        OrderedList.configure({
            HTMLAttributes: {
                class: 'v-nazih-ordered-list',
            }
        }),
        Blockquote.configure({
            HTMLAttributes: {
                class: 'v-nazih-blockquote',
            }
        }),
        Heading.configure({
            HTMLAttributes: {
                class: 'v-nazih-heading',
            }
        }),
    ]
})

const onSetHeading = (level) => {
    const isActive = editor.value.isActive({ heading: { level } });

    /**
     * If the heading is already active, we remove it for specific select text
     */
    editor.value.chain().focus().toggleHeading({ level }).run();

}

</script>

<style lang="scss">
.ProseMirror {
    padding: 6px 12px;
    min-height: 10rem;
    max-height: 20rem;
    overflow-y: auto;
}

.ProseMirror-focused {
    outline: 0;
}
</style>
