<template>
    Search for dogwalkers By Zip Code To Begin
    <div class="flex flex-col items-center gap-4">
      <input
        type="text"
        v-model="zip"
        placeholder="Zip Code"
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
      <button
        type="submit"
        @click="searchWalkers"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
      >Search</button>

      <!-- Display results -->
      <ul v-if="walkers.length" class="mt-4 w-full max-w-md">
        <li v-for="walker in walkers" :key="walker.id" 
         class="p-3 border rounded-lg cursor-pointer hover:bg-gray-100"
         @click="selectWalker(walker)">
          {{ walker.name }}
        </li>
        
      </ul>
      <div v-else-if="searched" class="mt-4 text-gray-500">No walkers found.</div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import WalkerCalendar from './WalkerCalendar.vue'
  
  const zip = ref('')
  const walkers = ref([])
  const searched = ref(false)
  const selectedWalkerId = ref(null) 
  
  async function searchWalkers() {
    searched.value = false
    try {
      const response = await fetch(`/getwalkers/${encodeURIComponent(zip.value)}`)
      if (!response.ok) throw new Error('Network response was not ok')
      const data = await response.json()
      walkers.value = data
      searched.value = true
    } catch (error) {
      walkers.value = []
      searched.value = true
      console.log("error getting dogwalkers.")
    }
  }

  // function selectWalker(walker) {
  //     console.log(walker.name)
  //     selectedWalkerId.value = selectedWalkerId.value === walker.id ? null : walker.id
  // }
 
  const emit = defineEmits(['walker-selected']);

  // Function to handle walker selection 
  function selectWalker(walker) {
    emit('walker-selected', walker);
  }
  </script>
  