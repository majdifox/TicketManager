<template>
  <div class="ticket-chat">
    <!-- Chat Header -->
    <div class="chat-header bg-blue-500 text-white p-3 rounded-t-lg">
      <h4 class="font-semibold">
        <i class="fas fa-comments mr-2"></i>
        Messages
      </h4>
      <div v-if="otherUser" class="text-sm opacity-90">
        Chat with {{ otherUser.name }} about {{ ticket.ticket_number }}
      </div>
    </div>

    <!-- Chat Status -->
    <div v-if="!canChat" class="p-4 bg-gray-100 text-center">
      <i class="fas fa-info-circle text-blue-500 mr-2"></i>
      {{ chatStatusMessage }}
    </div>

    <!-- Chat Messages -->
    <div v-else class="chat-container">
      <!-- Messages Area -->
      <div ref="messagesContainer" class="messages-area p-4 h-96 overflow-y-auto bg-gray-50">
        <div v-if="loading" class="text-center text-gray-500">
          <i class="fas fa-spinner fa-spin"></i> Loading messages...
        </div>
        
        <div v-else-if="messages.length === 0" class="text-center text-gray-500">
          <i class="fas fa-comment-dots text-3xl mb-2 block"></i>
          Start a conversation about your ticket
        </div>

        <div v-else>
          <div
            v-for="message in messages"
            :key="message.id"
            :class="[
              'message mb-3',
              message.from_id === currentUserId ? 'sent' : 'received'
            ]"
          >
            <div
              :class="[
                'message-bubble p-3 rounded-lg max-w-xs',
                message.from_id === currentUserId
                  ? 'bg-blue-500 text-white ml-auto'
                  : 'bg-white border shadow-sm'
              ]"
            >
              <div class="message-text">{{ cleanMessage(message.body) }}</div>
              <div
                :class="[
                  'message-time text-xs mt-1',
                  message.from_id === currentUserId ? 'text-blue-100' : 'text-gray-500'
                ]"
              >
                {{ formatTime(message.created_at) }}
                <i
                  v-if="message.from_id === currentUserId"
                  :class="[
                    'fas ml-1',
                    message.seen ? 'fa-check-double text-blue-200' : 'fa-check text-blue-300'
                  ]"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Message Input -->
      <div class="message-input p-3 bg-white border-t">
        <form @submit.prevent="sendMessage" class="flex gap-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type your message..."
            class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="sending"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim() || sending"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="sending" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-paper-plane"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'TicketChat',
  props: {
    ticket: {
      type: Object,
      required: true
    },
    currentUserId: {
      type: Number,
      required: true
    }
  },
  
  data() {
    return {
      messages: [],
      newMessage: '',
      loading: true,
      sending: false,
      canChat: false,
      otherUser: null,
      chatStatusMessage: 'Loading chat...',
      refreshInterval: null
    }
  },
  
  mounted() {
    this.initializeChat()
    this.startMessageRefresh()
  },
  
  beforeUnmount() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval)
    }
  },
  
  methods: {
    async initializeChat() {
      try {
        // Get chat status first - use correct URL format with /api/ prefix
        const statusResponse = await axios.get(`/api/tickets/${this.ticket.id}/chat/status`)
        
        if (statusResponse.data.can_chat) {
          this.canChat = true
          this.otherUser = statusResponse.data.other_user
          await this.loadMessages()
        } else {
          this.canChat = false
          this.chatStatusMessage = statusResponse.data.reason
        }
      } catch (error) {
        console.error('Failed to initialize chat:', error)
        // For debugging
        if (error.response) {
          console.error('Response status:', error.response.status)
          console.error('Response data:', error.response.data)
        }
        this.chatStatusMessage = 'Failed to load chat. Please refresh the page.'
      } finally {
        this.loading = false
      }
    },
    
    async loadMessages() {
      try {
        const response = await axios.get(`/api/tickets/${this.ticket.id}/chat/messages`)
        this.messages = response.data.messages || []
        this.otherUser = response.data.other_user
        this.$nextTick(() => {
          this.scrollToBottom()
        })
      } catch (error) {
        console.error('Failed to load messages:', error)
        if (error.response) {
          console.error('Response status:', error.response.status)
          console.error('Response data:', error.response.data)
        }
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return
      
      this.sending = true
      const message = this.newMessage.trim()
      this.newMessage = ''
      
      try {
        const response = await axios.post(`/api/tickets/${this.ticket.id}/chat/send`, {
          message: message
        })
        
        if (response.data.success) {
          // Add message to local array for immediate feedback
          this.messages.push(response.data.message)
          this.$nextTick(() => {
            this.scrollToBottom()
          })
        }
      } catch (error) {
        console.error('Failed to send message:', error)
        if (error.response) {
          console.error('Send error status:', error.response.status)
          console.error('Send error data:', error.response.data)
        }
        // Restore message if sending failed
        this.newMessage = message
        alert('Failed to send message. Please try again.')
      } finally {
        this.sending = false
      }
    },
    
    scrollToBottom() {
      if (this.$refs.messagesContainer) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight
      }
    },
    
    cleanMessage(messageBody) {
      // Remove ticket reference from display
      return messageBody.replace(/\n\n\[Ticket: #[^\]]+\]$/, '')
    },
    
    formatTime(timestamp) {
      return new Date(timestamp).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    
    startMessageRefresh() {
      // Refresh messages every 5 seconds if chat is active
      this.refreshInterval = setInterval(() => {
        if (this.canChat && !this.sending) {
          this.loadMessages()
        }
      }, 5000)
    }
  }
}
</script>

<style scoped>
.ticket-chat {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
}

.messages-area {
  background-image: 
    radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
}

.message {
  display: flex;
  flex-direction: column;
}

.message.sent {
  align-items: flex-end;
}

.message.received {
  align-items: flex-start;
}

.message-bubble {
  word-wrap: break-word;
  position: relative;
}

.message.sent .message-bubble {
  border-bottom-right-radius: 0.25rem;
}

.message.received .message-bubble {
  border-bottom-left-radius: 0.25rem;
}

.message-time {
  font-size: 0.75rem;
  margin-top: 0.25rem;
}
</style>