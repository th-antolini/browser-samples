<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-2">
          📝 Todo Manager
        </h1>
        <p class="text-gray-600 text-lg">Stay organized and get things done!</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
          <div class="text-3xl font-bold text-blue-600">{{ stats.total }}</div>
          <div class="text-gray-600">Total Tasks</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
          <div class="text-3xl font-bold text-green-600">{{ stats.completed }}</div>
          <div class="text-gray-600">Completed</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
          <div class="text-3xl font-bold text-orange-600">{{ stats.pending }}</div>
          <div class="text-gray-600">Pending</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
          <div class="text-3xl font-bold text-red-600">{{ stats.highPriority }}</div>
          <div class="text-gray-600">High Priority</div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Todo Form -->
        <div class="lg:col-span-1">
          <TodoForm @todo-added="handleTodoAdded" />
        </div>

        <!-- Todo List -->
        <div class="lg:col-span-2">
          <TodoList 
            :todos="todos" 
            :loading="loading"
            @todo-updated="handleTodoUpdated"
            @todo-deleted="handleTodoDeleted"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import TodoForm from './TodoForm.vue';
import TodoList from './TodoList.vue';

export default {
  name: 'TodoApp',
  components: {
    TodoForm,
    TodoList
  },
  setup() {
    const todos = ref([]);
    const loading = ref(false);

    const stats = computed(() => {
      return {
        total: todos.value.length,
        completed: todos.value.filter(todo => todo.completed).length,
        pending: todos.value.filter(todo => !todo.completed).length,
        highPriority: todos.value.filter(todo => todo.priority === 'high').length
      };
    });

    const fetchTodos = async () => {
      loading.value = true;
      try {
        const response = await fetch('/api/todos');
        const data = await response.json();
        if (data.success) {
          todos.value = data.data;
        }
      } catch (error) {
        console.error('Error fetching todos:', error);
      } finally {
        loading.value = false;
      }
    };

    const handleTodoAdded = (newTodo) => {
      todos.value.unshift(newTodo);
    };

    const handleTodoUpdated = (updatedTodo) => {
      const index = todos.value.findIndex(todo => todo.id === updatedTodo.id);
      if (index !== -1) {
        todos.value[index] = updatedTodo;
      }
    };

    const handleTodoDeleted = (deletedTodoId) => {
      todos.value = todos.value.filter(todo => todo.id !== deletedTodoId);
    };

    onMounted(() => {
      fetchTodos();
    });

    return {
      todos,
      loading,
      stats,
      handleTodoAdded,
      handleTodoUpdated,
      handleTodoDeleted
    };
  }
};
</script>