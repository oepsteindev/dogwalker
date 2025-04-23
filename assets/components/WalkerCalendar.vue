<template>
    <div class="p-4 border rounded-lg bg-white shadow-md">
      <h3 class="text-lg font-semibold mb-4">{{ walkerName }}'s Calendar</h3>
      
      <!-- Calendar Navigation -->
      <div class="flex justify-between items-center mb-4">
        <button @click="previousMonth" class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
          &lt; Previous
        </button>
        <h4 class="text-lg">{{ currentMonthName }} {{ currentYear }}</h4>
        <button @click="nextMonth" class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
          Next &gt;
        </button>
      </div>
  
      <!-- Calendar Grid -->
      <div class="grid grid-cols-7 gap-1">
        <!-- Days of week headers -->
        <div v-for="day in daysOfWeek" :key="day" class="text-center font-semibold p-2">
          {{ day }}
        </div>
  
        <!-- Calendar days -->
        <div
          v-for="(day, index) in calendarDays"
          :key="index"
          class="p-2 border text-center min-h-[60px]"
          :class="{
            'bg-gray-100': !day.isCurrentMonth,
            'cursor-pointer hover:bg-blue-50': day.isCurrentMonth,
            'bg-blue-100': isSelectedDate(day.date)
          }"
          @click="day.isCurrentMonth && selectDate(day.date)"
        >
          <div>{{ day.dayNumber }}</div>
          <div v-if="day.hasAppointment" class="text-xs text-green-600">
            Available
          </div>
        </div>
      </div>
  
      <!-- Appointment Section -->
      <div v-if="selectedDate" class="mt-4 p-4 border-t">
        <h4 class="font-semibold">Available Times for {{ formatDate(selectedDate) }}</h4>
        <div class="grid grid-cols-3 gap-2 mt-2">
          <button
            v-for="time in availableTimes"
            :key="time"
            @click="bookAppointment(time)"
            class="p-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            {{ time }}
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  
  const props = defineProps({
    walkerId: {
      type: String,
      required: true
    },
    walkerName: {
      type: String,
      required: true
    }
  })
  
  const currentDate = ref(new Date())
  const selectedDate = ref(null)
  const availableTimes = ref(['9:00 AM', '10:00 AM', '2:00 PM', '3:00 PM', '4:00 PM'])
  
  const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  
  const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('default', { month: 'long' })
  })
  
  const currentYear = computed(() => {
    return currentDate.value.getFullYear()
  })
  
  const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)
    
    const days = []
    
    // Add days from previous month
    const prevMonthDays = firstDay.getDay()
    const prevMonth = new Date(year, month, 0)
    for (let i = prevMonthDays - 1; i >= 0; i--) {
      days.push({
        date: new Date(year, month - 1, prevMonth.getDate() - i),
        dayNumber: prevMonth.getDate() - i,
        isCurrentMonth: false,
        hasAppointment: false
      })
    }
    
    // Add days from current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
      days.push({
        date: new Date(year, month, i),
        dayNumber: i,
        isCurrentMonth: true,
        hasAppointment: Math.random() > 0.5 // Simulate random availability
      })
    }
    
    // Add days from next month
    const remainingDays = 42 - days.length // 6 rows * 7 days = 42
    for (let i = 1; i <= remainingDays; i++) {
      days.push({
        date: new Date(year, month + 1, i),
        dayNumber: i,
        isCurrentMonth: false,
        hasAppointment: false
      })
    }
    
    return days
  })
  
  function previousMonth() {
    currentDate.value = new Date(
      currentDate.value.getFullYear(),
      currentDate.value.getMonth() - 1,
      1
    )
  }
  
  function nextMonth() {
    currentDate.value = new Date(
      currentDate.value.getFullYear(),
      currentDate.value.getMonth() + 1,
      1
    )
  }
  
  function selectDate(date) {
    selectedDate.value = date
  }
  
  function isSelectedDate(date) {
    if (!selectedDate.value) return false
    return date.toDateString() === selectedDate.value.toDateString()
  }
  
  function formatDate(date) {
    return date.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }
  
  async function bookAppointment(time) {
    if (!selectedDate.value) return
    
    try {
      // Here you would typically make an API call to book the appointment
      const appointment = {
        walkerId: props.walkerId,
        date: selectedDate.value,
        time: time
      }
      
      // Simulate API call
      console.log('Booking appointment:', appointment)
      alert(`Appointment booked with ${props.walkerName} for ${formatDate(selectedDate.value)} at ${time}`)
    } catch (error) {
      console.error('Error booking appointment:', error)
      alert('Failed to book appointment')
    }
  }
  
  // You would typically fetch the walker's availability here
  onMounted(async () => {
    try {
      // Simulate API call to get walker's availability
      console.log(`Fetching availability for walker ${props.walkerId}`)
    } catch (error) {
      console.error('Error fetching availability:', error)
    }
  })
  </script>