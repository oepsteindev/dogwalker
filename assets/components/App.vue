
<script setup>
import Welcome from './Welcome.vue'
import SearchDogwalkers from './SearchDogwalkers.vue'
import { ref, onMounted} from 'vue'
import SimpleCalendar from './SimpleCalendar.vue';
import EventForm from './EventForm.vue';
import { createCustomer } from '../js/customerService.js';
import CustomerForm from './CustomerForm.vue';




const API_URL = '/api/calendar';
const showCustomerForm = ref(false);
const userId = document.getElementById('user-id').value

const selectedDate = ref(null);
const eventFormData = ref({
  title: '',
  time: '',
  color: '#4CAF50',
  notes: ''
});
// const events = ref([
//   {
//     id: 1,
//     title: 'Team Meeting',
//     date: new Date(2025, 3, 22), // April 22, 2025
//     time: '10:00 AM',
//     color: '#3498db'
//   },
//   {
//     id: 2,
//     title: 'Doctor Appointment',
//     date: new Date(2025, 3, 24), // April 24, 2025
//     time: '2:30 PM',
//     color: '#e74c3c'
//   }
// ]);
const events = ref([])
const showEventForm = ref(false);

onMounted(async () => {
  const link = document.getElementById('add-customer-link');
  if (link) {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      showCustomerForm.value = true;
    });
  }
  try {
    listevents();
  } catch (error) {
    console.error('Failed to load events:', error);
  }

});
async function listevents() {  
  try{
  const response = await fetch('/api/calendar/listevents');
    console.log('fetching;')
    if (response.ok) {
      events.value = await response.json();
    }
  } catch (error) {
    console.error('Failed to load events:', error);
  }
}


function handleDateSelected(date) {
  console.log('Selected date:', date);
}
function openEventForm(date) {
  console.log('Add event for date:', date);
  selectedDate.value = date;
  
  // Reset form data
  eventFormData.value = {
    title: '',
    time: '',
    color: '#4CAF50',
    notes: ''
  };
  
  // Show the form
  showEventForm.value = true;
}

function closeEventForm() {
  showEventForm.value = false;
}  

function saveEvent() {
  // Create new event from form data
  console.log('Saving event:', eventFormData.value)
  const newEvent = {
    id: Date.now(), // Simple unique ID
    title: eventFormData.value.title,
    date: eventFormData.value.date,
    time: eventFormData.value.time,
    color: eventFormData.value.color,
    notes: eventFormData.value.notes,
    startDateTime: selectedDate.value,
    customerId: userId
  };
  // console.log('Sending startDateTime:', selectedDate.value);
  // Add to events array - make sure events is defined as a ref
  events.value.push(newEvent);
  
  axios.post(`${API_URL}/events`, newEvent)
    .then(response => {
      console.log('Event saved:', response.data);
      console.log('Updated events:', events.value);
    })
    .catch(error => {
      console.error('Error saving event:', error);
    });

  console.log('Event saved:', newEvent);
  console.log('Updated events:', events.value);
  
  // Close the form
  showEventForm.value = false;
  // Refresh calendar after saving event
  listevents()

}



</script>

<template>
  <div class="app">
    <div class="min-h-screen flex flex-col items-center justify-center gap-8">
      <CustomerForm v-if="showCustomerForm" @close="showCustomerForm = false" />
    <Welcome />
    <SearchDogwalkers />
    <SimpleCalendar 
      :events="events" 
      @date-selected="handleDateSelected"
      @add-event="openEventForm" 
    />
    <div v-if="showEventForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <EventForm 
      :selectedDate="selectedDate" 
      :eventData="eventFormData"
      @save="saveEvent" 
      @cancel="closeEventForm" 
    />
  </div>

  </div>
  </div>
</template>

