<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
      ➕ Add New Task
    </h2>
    
    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
          Task Title *
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          placeholder="Enter task title..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          :class="{ 'border-red-500': errors.title }"
        />
        <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          Description
        </label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
          placeholder="Enter task description..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
          :class="{ 'border-red-500': errors.description }"
        ></textarea>
        <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description[0] }}</p>
      </div>

      <!-- Priority and Due Date Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Priority -->
        <div>
          <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
            Priority *
          </label>
          <select
            id="priority"
            v-model="form.priority"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :class="{ 'border-red-500': errors.priority }"
          >
            <option value="low">🟢 Low</option>
            <option value="medium">🟡 Medium</option>
            <option value="high">🔴 High</option>
          </select>
          <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority[0] }}</p>
        </div>

        <!-- Due Date -->
        <div>
          <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
            Due Date
          </label>
          <input
            id="due_date"
            v-model="form.due_date"
            type="datetime-local"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :class="{ 'border-red-500': errors.due_date }"
          />
          <p v-if="errors.due_date" class="text-red-500 text-sm mt-1">{{ errors.due_date[0] }}</p>
        </div>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium py-3 px-4 rounded-md transition duration-200 flex items-center justify-center space-x-2"
      >
        <span v-if="loading" class="animate-spin">⏳</span>
        <span>{{ loading ? 'Adding Task...' : 'Add Task' }}</span>
      </button>
    </form>

    <!-- Success Message -->
    <div v-if="showSuccess" class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md">
      ✅ Task added successfully!
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue';

export default {
  name: 'TodoForm',
  emits: ['todo-added'],
  setup(props, { emit }) {
    const loading = ref(false);
    const showSuccess = ref(false);
    const errors = ref({});

    const form = reactive({
      title: '',
      description: '',
      priority: 'medium',
      due_date: ''
    });

    const resetForm = () => {
      form.title = '';
      form.description = '';
      form.priority = 'medium';
      form.due_date = '';
      errors.value = {};
    };

    const showSuccessMessage = () => {
      showSuccess.value = true;
      setTimeout(() => {
        showSuccess.value = false;
      }, 3000);
    };

    const submitForm = async () => {
      loading.value = true;
      errors.value = {};

      try {
        const response = await fetch('/api/todos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify(form)
        });

        const data = await response.json();

        if (response.ok && data.success) {
          emit('todo-added', data.data);
          resetForm();
          showSuccessMessage();
        } else {
          if (data.errors) {
            errors.value = data.errors;
          } else {
            console.error('Error adding todo:', data.message);
          }
        }
      } catch (error) {
        console.error('Error adding todo:', error);
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      showSuccess,
      errors,
      submitForm
    };
  }
};
</script>