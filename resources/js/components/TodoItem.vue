<template>
  <div class="border border-gray-200 rounded-lg p-4 transition-all duration-200 hover:shadow-md" 
       :class="{ 'bg-gray-50 opacity-75': todo.completed }">
    
    <!-- View Mode -->
    <div v-if="!isEditing" class="flex items-start gap-3">
      <!-- Checkbox -->
      <button
        @click="toggleComplete"
        :disabled="loading"
        class="mt-1 flex-shrink-0 w-5 h-5 border-2 rounded transition-colors duration-200"
        :class="todo.completed 
          ? 'bg-green-500 border-green-500 text-white' 
          : 'border-gray-300 hover:border-green-400'"
      >
        <span v-if="todo.completed" class="text-xs">✓</span>
      </button>

      <!-- Content -->
      <div class="flex-grow min-w-0">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
          <div class="flex-grow min-w-0">
            <h3 class="font-semibold text-gray-900 truncate" 
                :class="{ 'line-through text-gray-500': todo.completed }">
              {{ todo.title }}
            </h3>
            <p v-if="todo.description" 
               class="text-gray-600 text-sm mt-1"
               :class="{ 'line-through': todo.completed }">
              {{ todo.description }}
            </p>
            
            <!-- Meta Information -->
            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
              <span class="flex items-center gap-1">
                <span :class="priorityClass">●</span>
                {{ capitalizeFirst(todo.priority) }} Priority
              </span>
              <span v-if="todo.due_date" class="flex items-center gap-1">
                📅 {{ formatDate(todo.due_date) }}
              </span>
              <span class="flex items-center gap-1">
                🕒 {{ formatRelativeTime(todo.created_at) }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <button
              @click="startEdit"
              class="p-1 text-gray-400 hover:text-blue-600 transition-colors duration-200"
              title="Edit task"
            >
              ✏️
            </button>
            <button
              @click="deleteTodo"
              :disabled="loading"
              class="p-1 text-gray-400 hover:text-red-600 transition-colors duration-200"
              title="Delete task"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Mode -->
    <div v-else class="space-y-3">
      <input
        v-model="editForm.title"
        type="text"
        placeholder="Task title..."
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
      />
      <textarea
        v-model="editForm.description"
        rows="2"
        placeholder="Task description..."
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
      ></textarea>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <select
          v-model="editForm.priority"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="low">🟢 Low Priority</option>
          <option value="medium">🟡 Medium Priority</option>
          <option value="high">🔴 High Priority</option>
        </select>
        <input
          v-model="editForm.due_date"
          type="datetime-local"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      <div class="flex justify-end gap-2">
        <button
          @click="cancelEdit"
          class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
        >
          Cancel
        </button>
        <button
          @click="saveEdit"
          :disabled="loading || !editForm.title.trim()"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-md transition-colors duration-200"
        >
          {{ loading ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed } from 'vue';

export default {
  name: 'TodoItem',
  props: {
    todo: {
      type: Object,
      required: true
    }
  },
  emits: ['updated', 'deleted'],
  setup(props, { emit }) {
    const loading = ref(false);
    const isEditing = ref(false);
    
    const editForm = reactive({
      title: '',
      description: '',
      priority: '',
      due_date: ''
    });

    const priorityClass = computed(() => {
      const classes = {
        low: 'text-green-500',
        medium: 'text-yellow-500',
        high: 'text-red-500'
      };
      return classes[props.todo.priority] || 'text-gray-500';
    });

    const startEdit = () => {
      editForm.title = props.todo.title;
      editForm.description = props.todo.description || '';
      editForm.priority = props.todo.priority;
      editForm.due_date = props.todo.due_date ? 
        new Date(props.todo.due_date).toISOString().slice(0, 16) : '';
      isEditing.value = true;
    };

    const cancelEdit = () => {
      isEditing.value = false;
    };

    const saveEdit = async () => {
      if (!editForm.title.trim()) return;

      loading.value = true;
      try {
        const response = await fetch(`/api/todos/${props.todo.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            title: editForm.title,
            description: editForm.description || null,
            priority: editForm.priority,
            due_date: editForm.due_date || null
          })
        });

        const data = await response.json();
        if (data.success) {
          emit('updated', data.data);
          isEditing.value = false;
        }
      } catch (error) {
        console.error('Error updating todo:', error);
      } finally {
        loading.value = false;
      }
    };

    const toggleComplete = async () => {
      loading.value = true;
      try {
        const response = await fetch(`/api/todos/${props.todo.id}/toggle`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });

        const data = await response.json();
        if (data.success) {
          emit('updated', data.data);
        }
      } catch (error) {
        console.error('Error toggling todo:', error);
      } finally {
        loading.value = false;
      }
    };

    const deleteTodo = async () => {
      if (!confirm('Are you sure you want to delete this task?')) return;

      loading.value = true;
      try {
        const response = await fetch(`/api/todos/${props.todo.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });

        const data = await response.json();
        if (data.success) {
          emit('deleted', props.todo.id);
        }
      } catch (error) {
        console.error('Error deleting todo:', error);
      } finally {
        loading.value = false;
      }
    };

    const capitalizeFirst = (str) => {
      return str.charAt(0).toUpperCase() + str.slice(1);
    };

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    };

    const formatRelativeTime = (dateString) => {
      const date = new Date(dateString);
      const now = new Date();
      const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
      
      if (diffInHours < 1) return 'Just now';
      if (diffInHours < 24) return `${diffInHours}h ago`;
      const diffInDays = Math.floor(diffInHours / 24);
      if (diffInDays < 7) return `${diffInDays}d ago`;
      return date.toLocaleDateString();
    };

    return {
      loading,
      isEditing,
      editForm,
      priorityClass,
      startEdit,
      cancelEdit,
      saveEdit,
      toggleComplete,
      deleteTodo,
      capitalizeFirst,
      formatDate,
      formatRelativeTime
    };
  }
};
</script>