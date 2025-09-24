import { useUserStore } from "@/stores/UserStore";
import { defineStore } from 'pinia';
import { nextTick } from "vue";
import router from "@/routes";
import axios from 'axios';

// Interfaces
interface Contact {
    id: number;
    name: string;
    avatar_url: string;
    is_business: boolean;
    slug: string;
}

interface Conversation {
    id: number;
    contact: Contact;
    owner: Contact;
    advertisement?: any;
    last_message: string;
    last_message_created_at: string;
    last_message_date_time: string;
    is_read: boolean;
    has_messages: boolean;
    total_unread: number;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
    page: number;
}

interface Message {
    id: number;
    body: string;
    is_sender: boolean;
    created_at: string;
}


// Store
export const useCustomerChatStore = defineStore('chat', {
    state: () => ({
        conversations: {
            data: [] as Conversation[],
            current_page: 1,
            first_page_url: '',
            from: 0,
            last_page: 0,
            last_page_url: '',
            next_page_url: '',
            path: '',
            per_page: 0,
            prev_page_url: '',
            to: 0,
            total: 0,
            page: 1,
        } as PaginatedData<Conversation>,
        messages: {
            data: [] as Message[],
            current_page: 1,
            first_page_url: '',
            from: 0,
            last_page: 0,
            last_page_url: '',
            next_page_url: '',
            path: '',
            per_page: 0,
            prev_page_url: '',
            to: 0,
            total: 0,
            page: 1,
        } as PaginatedData<Message>,
        message: '',
        totalUnreadMessages: 0,
        isChatOpen: false,
        currentConversation: null as Conversation | null,
        isLoadingMessages: true,
        isLoadingConversations: true,
    }),
    getters: {
      conversations_by_date():any {
          return this.conversations.data.sort((a, b) => {
            return new Date(b.last_message_date_time).getTime() - new Date(a.last_message_date_time).getTime();
          });
      }
    },
    actions: {
        // fetchConversations method is called when the user opens the chat add param page as optional
        async fetchConversations({page, type}: { page?: number, type?: string }) {
            this.isLoadingConversations = true;

            if(page) {
                this.conversations.page = page;
            }


            let params = { page: this.conversations.page } as { page: number, type?: string };

            if(type) {
                params.type = type;
            }

            try {
                const { data } = await axios.get(`/chat/conversations`, { params });

                if(data.ok) {
                    this.conversations          = data.conversations;
                    this.totalUnreadMessages    = data.total_unread_messages;
                }

                return data.ok;
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoadingConversations = false;
            }
        },

        // This method is called when the user clicks on a conversation
        async fetchMessages({ conversationId, page }: { conversationId: number|undefined, page?: number }) {
            if(!conversationId) return console.error('Conversation ID is required');

            if(!page && this.messages.page > 1) {
                this.isLoadingMessages  = true;
            }

            if(page) {
                this.messages.page = page;
            }

            try {
                const { data } = await axios.get(`/chat/messages`, { params: { conversationId, page: this.messages.page } });

                if(data.ok) {
                    this.messages = {
                        data: this.messages.page === 1 ? data.messages.data : this.messages.data.concat(data.messages.data),
                        first_page_url: data.messages.first_page_url,
                        last_page_url: data.messages.last_page_url,
                        next_page_url: data.messages.next_page_url,
                        prev_page_url: data.messages.prev_page_url,
                        current_page: data.messages.current_page,
                        last_page: data.messages.last_page,
                        per_page: data.messages.per_page,
                        total: data.messages.total,
                        from: data.messages.from,
                        path: data.messages.path,
                        page: this.messages.page,
                        to: data.messages.to,
                    };
                }

                return data.ok;
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoadingMessages = false;
            }
        },

        // This method is called when the user opens a conversation
        async openConversation(conversationData: any) {
            this.isLoadingMessages  = true;
            this.isChatOpen         = true;

            try {
                const { data } = await axios.post(`/chat/open`, conversationData);

                if(data.ok) {
                    this.currentConversation = data.conversation;

                    await nextTick(async() => await this.fetchMessages({
                        conversationId: data.conversation.id
                    }).then(async (ok) => {
                        if(ok) {
                            setTimeout(async () => {
                                const messagesContainer = document.getElementsByClassName('messenger__right__content__body')[0];

                                if (messagesContainer) {
                                    messagesContainer.scrollTop = this.messages.page <= 1 ? 80 : messagesContainer.scrollHeight;
                                }
                            }, 100);
                        }
                    }));
                }
            } catch (error) {
                await router.push({ name: '404' });
            } finally {
                this.isLoadingMessages = false;
            }
        },

        // This method is called when the user sends a message
        async sendMessage() {
            if(!this.message || !this.currentConversation?.id) return;

            try {
                const { data } = await axios.post(`/chat/send`, {
                    message: this.message,
                    conversationId: this.currentConversation.id,
                });

                if(data.ok) {
                    this.message = '';
                }
            } catch (error) {
                console.error(error);
            }
        },

        ///Mark as read is called when the user clicks on a conversation
        async markAsRead() {
            try {
                const { data } = await axios.post(`/chat/mark-as-read`);

                if(data.ok) {
                    this.totalUnreadMessages = 0;

                    this.conversations.data.forEach((conversation) => {
                        conversation.is_read = true;
                    });
                }

                return data.ok;
            } catch (error) {
                console.error(error);
            }
        },

        // This method is called when a new message is received
        async addMessage(message: any) {
            if(this.isChatOpen && this.currentConversation?.id === message.conversation.id) {
                this.messages.data.unshift({
                    is_sender: useUserStore().me.id === message.open.sender_id,
                    created_at: message.open.created_at,
                    body: message.open.body,
                    id: message.open.id,
                });

                const messagesContainer = document.getElementsByClassName('messenger__right__content__body')[0];

                if(messagesContainer)
                {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }

                let existsConversation = this.conversations.data.find((conversation) => conversation.id === message.conversation.id);

                if(existsConversation) {
                    existsConversation.last_message             = message.open.body;
                    existsConversation.last_message_created_at  = message.open.created_at;
                    existsConversation.last_message_date_time   = message.open.created_at;
                    existsConversation.total_unread             = 0;
                }

                return await this.markAsRead();
            }

            await this.fetchConversations({ page: 1 });
        }
    },
});
