<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <h2 class="text-2xl font-bold text-gray-800">
        📋 My Tasks
      </h2>
      
      <!-- Search and Filter -->
      <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search tasks..."
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <select
          v-model="filterStatus"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="all">All Tasks</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin text-4xl mb-4">⏳</div>
      <p class="text-gray-600">Loading tasks...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredTodos.length === 0" class="text-center py-12">
      <div class="text-6xl mb-4">📝</div>
      <h3 class="text-xl font-semibold text-gray-700 mb-2">
        {{ searchQuery || filterStatus !== 'all' ? 'No tasks found' : 'No tasks yet' }}
      </h3>
      <p class="text-gray-600">
        {{ searchQuery || filterStatus !== 'all' ? 'Try adjusting your search or filter.' : 'Add your first task to get started!' }}
      </p>
    </div>

    <!-- Todo Items -->
    <div v-else class="space-y-3">
      <TodoItem
        v-for="todo in filteredTodos"
        :key="todo.id"
        :todo="todo"
        @updated="handleTodoUpdate"
        @deleted="handleTodoDelete"
      />
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';
import TodoItem from './TodoItem.vue';

export default {
  name: 'TodoList',
  components: {
    TodoItem
  },
  props: {
    todos: {
      type: Array,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['todo-updated', 'todo-deleted'],
  setup(props, { emit }) {
    const searchQuery = ref('');
    const filterStatus = ref('all');

    const filteredTodos = computed(() => {
      let filtered = props.todos;

      // Filter by status
      if (filterStatus.value === 'completed') {
        filtered = filtered.filter(todo => todo.completed);
      } else if (filterStatus.value === 'pending') {
        filtered = filtered.filter(todo => !todo.completed);
      }

      // Filter by search query
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(todo => 
          todo.title.toLowerCase().includes(query) ||
          (todo.description && todo.description.toLowerCase().includes(query))
        );
      }

      return filtered;
    });

    const handleTodoUpdate = (updatedTodo) => {
      emit('todo-updated', updatedTodo);
    };

    const handleTodoDelete = (deletedTodoId) => {
      emit('todo-deleted', deletedTodoId);
    };

    return {
      searchQuery,
      filterStatus,
      filteredTodos,
      handleTodoUpdate,
      handleTodoDelete
    };
  }
};
</script>