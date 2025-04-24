<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  selectedDate: {
    type: Date,
    required: true
  },
  eventData: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['save', 'cancel']);

const colorOptions = [
  { value: '#4CAF50', label: 'Green' },
  { value: '#2196F3', label: 'Blue' },
  { value: '#F44336', label: 'Red' },
  { value: '#FF9800', label: 'Orange' },
  { value: '#9C27B0', label: 'Purple' }
];

function formatDate(date) {
  if (!date) return '';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
}

function handleSubmit() {
  // Validate form
  if (!props.eventData.title.trim()) {
    alert('Please enter an event title');
    return;
  }
  
  if (!props.eventData.time.trim()) {
    alert('Please enter a time');
    return;
  }
  
  emit('save');
}
</script>

<template>
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
    <h2 class="text-2xl font-bold mb-4">Add New Appointment</h2>
    
    <div class="mb-4">
      <p class="text-gray-600">
        For: <span class="font-semibold">{{ formatDate(selectedDate) }}</span>
      </p>
    </div>
    
    <form @submit.prevent="$emit('save')">
      <!-- Title -->
      <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input 
          id="title" 
          v-model="eventData.title" 
          type="text" 
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="e.g., Dog Walk with Fido"
        >
      </div>
      
      <!-- Time -->
      <div class="mb-4">
        <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
        <input 
          id="time" 
          v-model="eventData.time" 
          type="time" 
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
      </div>
      
      <!-- Color -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
        <div class="flex space-x-2">
          <button 
            v-for="color in colorOptions" 
            :key="color.value"
            type="button"
            @click="eventData.color = color.value"
            :class="[
              'w-8 h-8 rounded-full border-2',
              eventData.color === color.value ? 'border-black' : 'border-transparent'
            ]"
            :style="{ backgroundColor: color.value }"
            :title="color.label"
          ></button>
        </div>
      </div>
      
      <!-- Notes -->
      <div class="mb-6">
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea 
          id="notes" 
          v-model="eventData.notes" 
          rows="3" 
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Any special instructions for the dog walker?"
        ></textarea>
      </div>
      
      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <button 
          type="button" 
          @click="emit('cancel')"
          class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
        >
          Cancel
        </button>
        <button 
            type="button" 
            @click="$emit('save')"
            class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
            Save Appointment
            </button>
      </div>
    </form>
  </div>
</template>