<script setup>
import Welcome from './Welcome.vue'
import SearchDogwalkers from './SearchDogwalkers.vue'
import { ref, onMounted} from 'vue'
import SimpleCalendar from './SimpleCalendar.vue';
import EventForm from './EventForm.vue';
import { createCustomer } from '../js/customerService.js';
import CustomerForm from './CustomerForm.vue';
import ConfirmationModal from  './Confirmation.vue';

//ref to control component visibility
const showCalendar = ref(false);
const selectedWalker = ref(null);

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

// Add new refs for confirmation modal
const showConfirmationModal = ref(false);
const confirmationData = ref({
   date: null,
   time: '',
   walkerName: ''
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

 // Show confirmation modal with event details

 confirmationData.value = {
   date: formatDate(selectedDate.value),
   time: eventFormData.value.time,
   walkerName: selectedWalker.value?.name || 'Unknown'
 };
 showConfirmationModal.value = true;
 showEventForm.value = false;
}

function closeConfirmationModal() {
  showConfirmationModal.value = false;
}

function formatDate(date) {
   if (!date) return '';
   const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
   return new Date(date).toLocaleDateString(undefined, options);
 }


// Add new function to handle walker selection
function handleWalkerSelected(walker) {
  selectedWalker.value = walker;
  showCalendar.value = true;
}
</script>

<template>
  <header class="w-full h-[200px] sticky top-0" style="background-image: url('https://t4.ftcdn.net/jpg/01/22/84/71/360_F_122847119_LaRWFiqPDk6aFI7awhGjqaGoqmVedePo.jpg'); background-size: contain; background-repeat: no-repeat; background-position: center; background-origin: content-box;"></header>
  <div class="app">
    <div class="h-screen flex flex-col items-center justify-start gap-8 pt-16">
      <CustomerForm v-if="showCustomerForm" @close="showCustomerForm = false" />
      <Welcome />
      
      <!-- Show SearchDogwalkers by default -->
      <SearchDogwalkers v-if="!showCalendar" @walker-selected="handleWalkerSelected" />
      
      <!-- Show SimpleCalendar only after a walker is selected -->
      <div v-if="showCalendar" class="w-full">
        <div class="mb-4">
          <h3 class="text-lg font-medium">Selected Dog Walker: {{ selectedWalker?.name }}</h3>
          <button @click="showCalendar = false" class="text-blue-600 hover:underline">
            &larr; Back to walker list
          </button>
        </div>
        <SimpleCalendar
          :events="events"
          @date-selected="handleDateSelected"
          @add-event="openEventForm"
        />
      </div>
      
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

  <!-- Confirmation Modal -->
  <div v-if="showConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <ConfirmationModal
      :date="confirmationData.date"
      :time="confirmationData.time"
      :walkerName="confirmationData.walkerName"
      @close="closeConfirmationModal"
    />
  </div>
</template>