// ConfirmationModal.vue
<template>
  <div class="bg-white p-6 rounded shadow-lg">
    <h3 class="text-xl font-bold mb-2 text-green-600">Booking Confirmed!</h3>
    <p class="mb-4 text-gray-700">Congratulations, we'll see you then!</p>
    
    <p class="mb-4"><span class="font-medium">Date:</span> {{ date }}</p>
    <p class="mb-4"><span class="font-medium">Time:</span> {{ time }}</p>
    <p class="mb-4"><span class="font-medium">Dog Walker:</span> {{ walkerName }}</p>
    
    <div v-if="notes" class="mb-4">
      <p class="font-medium">Notes:</p>
      <p class="bg-gray-50 p-3 rounded mt-1 text-gray-700">{{ notes }}</p>
    </div>
    
    <div class="flex justify-end gap-2">
      <button @click="$emit('close')" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Close</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  date: String,
  time: String,
  walkerName: String,
  notes: String
});

defineEmits(['close']);
</script>