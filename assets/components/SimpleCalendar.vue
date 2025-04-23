// SimpleCalendar.vue
<template>
  <div class="calendar-container">
    <div class="calendar-header">
      <button @click="previousMonth">&lt;</button>
      <h2>{{ currentMonthName }} {{ currentYear }}</h2>
      <button @click="nextMonth">&gt;</button>
    </div>
    
    <div class="weekdays">
      <div v-for="day in weekdays" :key="day" class="weekday">{{ day }}</div>
    </div>
    
    <div class="calendar-grid">
      <div 
        v-for="day in calendarDays" 
        :key="day.date" 
        class="calendar-day"
        :class="{ 
          'current-month': day.currentMonth, 
          'other-month': !day.currentMonth,
          'selected': isSelected(day.date),
          'today': isToday(day.date)
        }"
        @click="selectDate(day.date)"
      >
        <span class="day-number">{{ day.dayNumber }}</span>
        <div v-if="getEventsForDate(day.date).length > 0" class="events-indicator">
          <div 
            v-for="event in getEventsForDate(day.date)" 
            :key="event.id" 
            class="event-dot"
            :style="{ backgroundColor: event.color || '#3498db' }"
            :title="event.title"
          ></div>
        </div>
      </div>
    </div>
    
    <div v-if="selectedDate" class="selected-date-events">
      <h3>{{ formatSelectedDate }}</h3>
      <div v-if="selectedDateEvents.length === 0" class="no-events">
        No events scheduled
      </div>
      <div v-else class="events-list">
        <div v-for="event in selectedDateEvents" :key="event.id" class="event-item">
          <div class="event-color" :style="{ backgroundColor: event.color || '#3498db' }"></div>
          <div class="event-details">
            <div class="event-title">{{ event.title }}</div>
            <div class="event-time">{{ event.time || 'All day' }}</div>
          </div>
        </div>
      </div>
      <button class="add-event-btn" @click="$emit('add-event', selectedDate)">
        Add Event
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['date-selected', 'add-event']);

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const currentDate = ref(new Date());
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const selectedDate = ref(null);

const currentMonthName = computed(() => {
  return new Date(currentYear.value, currentMonth.value).toLocaleString('default', { month: 'long' });
});

const daysInMonth = computed(() => {
  return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
  return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

const calendarDays = computed(() => {
  const days = [];
  
  // Previous month days
  const prevMonthDays = new Date(currentYear.value, currentMonth.value, 0).getDate();
  for (let i = firstDayOfMonth.value - 1; i >= 0; i--) {
    const dayNumber = prevMonthDays - i;
    const date = new Date(currentYear.value, currentMonth.value - 1, dayNumber);
    days.push({
      date,
      dayNumber,
      currentMonth: false
    });
  }
  
  // Current month days
  for (let i = 1; i <= daysInMonth.value; i++) {
    const date = new Date(currentYear.value, currentMonth.value, i);
    days.push({
      date,
      dayNumber: i,
      currentMonth: true
    });
  }
  
  // Next month days
  const remainingDays = 42 - days.length; // 6 rows * 7 days = 42
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(currentYear.value, currentMonth.value + 1, i);
    days.push({
      date,
      dayNumber: i,
      currentMonth: false
    });
  }
  
  return days;
});

const selectedDateEvents = computed(() => {
  if (!selectedDate.value) return [];
  return getEventsForDate(selectedDate.value);
});

const formatSelectedDate = computed(() => {
  if (!selectedDate.value) return '';
  return selectedDate.value.toLocaleDateString('en-US', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
});

function previousMonth() {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
}

function nextMonth() {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
}

function selectDate(date) {
  selectedDate.value = date;
  emit('date-selected', date);
}

function isSelected(date) {
  if (!selectedDate.value) return false;
  return date.toDateString() === selectedDate.value.toDateString();
}

function isToday(date) {
  return date.toDateString() === new Date().toDateString();
}

function getEventsForDate(date) {
  if (!date) return [];
  
  return props.events.filter(event => {
    const eventDate = new Date(event.date);
    return eventDate.toDateString() === date.toDateString();
  });
}

onMounted(() => {
  // Initialize with today selected
  selectDate(new Date());
});
</script>

<style scoped>
.calendar-container {
  display: flex;
  flex-direction: column;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  max-width: 900px;
  margin: 0 auto;
  font-family: Arial, sans-serif;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background-color: #f8f9fa;
  border-bottom: 1px solid #ddd;
}

.calendar-header button {
  background: #4a90e2;
  border: none;
  border-radius: 4px;
  color: white;
  cursor: pointer;
  font-size: 16px;
  padding: 8px 12px;
}

.weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background-color: #f8f9fa;
  border-bottom: 1px solid #ddd;
}

.weekday {
  padding: 10px;
  text-align: center;
  font-weight: bold;
  color: #333;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-template-rows: repeat(6, 1fr);
}

.calendar-day {
  min-height: 80px;
  border: 1px solid #eee;
  padding: 5px;
  cursor: pointer;
  position: relative;
}

.calendar-day:hover {
  background-color: #f5f5f5;
}

.current-month {
  color: #333;
}

.other-month {
  color: #aaa;
  background-color: #f9f9f9;
}

.selected {
  background-color: #e6f3ff;
  box-shadow: inset 0 0 0 2px #4a90e2;
}

.today {
  font-weight: bold;
}

.today .day-number {
  background-color: #4a90e2;
  color: white;
  border-radius: 50%;
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.day-number {
  margin-bottom: 5px;
}

.events-indicator {
  display: flex;
  gap: 2px;
  flex-wrap: wrap;
  margin-top: 5px;
}

.event-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.selected-date-events {
  padding: 15px;
  border-top: 1px solid #ddd;
  background-color: #f8f9fa;
}

.events-list {
  margin-top: 10px;
}

.event-item {
  display: flex;
  margin-bottom: 8px;
  padding: 8px;
  background-color: white;
  border-radius: 4px;
  border: 1px solid #eee;
}

.event-color {
  width: 12px;
  height: 100%;
  margin-right: 10px;
  border-radius: 2px;
}

.event-details {
  flex: 1;
}

.event-title {
  font-weight: bold;
}

.event-time {
  color: #666;
  font-size: 0.9em;
  margin-top: 3px;
}

.no-events {
  color: #666;
  font-style: italic;
  padding: 10px 0;
}

.add-event-btn {
  margin-top: 15px;
  padding: 8px 16px;
  background-color: #4a90e2;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.add-event-btn:hover {
  background-color: #357abd;
}
</style>