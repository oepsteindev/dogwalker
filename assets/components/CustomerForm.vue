<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-6 text-gray-800">Add New Customer</h2>
        
        <form @submit.prevent="submitForm" class="space-y-4">
          <div class="flex flex-col">
            <label for="name" class="text-sm font-medium text-gray-700 mb-1">Name</label>
            <input 
              id="name"
              v-model="form.name" 
              placeholder="Enter customer name" 
              required 
              class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="flex flex-col">
            <label for="email" class="text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              id="email"
              v-model="form.email" 
              placeholder="customer@example.com" 
              required 
              type="email" 
              class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="flex flex-col">
            <label for="phone" class="text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input 
              id="phone"
              v-model="form.phone" 
              placeholder="(123) 456-7890" 
              class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="flex flex-col">
            <label for="address" class="text-sm font-medium text-gray-700 mb-1">Address</label>
            <input 
              id="address"
              v-model="form.address" 
              placeholder="123 Main St, City, State" 
              class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="flex justify-between items-center pt-4">
            <button 
              type="button" 
              @click="close" 
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
            >
              Cancel
            </button>
            
            <button 
              type="submit" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Add Customer
            </button>
          </div>
        </form>
        
        <div 
          v-if="message" 
          class="mt-4 p-3 rounded-md"
          :class="{ 'bg-green-100 text-green-700': !message.includes('Error'), 'bg-red-100 text-red-700': message.includes('Error') }"
        >
          {{ message }}
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { createCustomer } from '../js/customerService.js';
  
  export default {
    data() {
      return {
        form: { name: '', email: '', phone: '', address: '' },
        message: ''
      };
    },
    methods: {
      async submitForm() {
        try {
          await createCustomer(this.form);
          this.message = "Customer added successfully!";
          this.form = { name: '', email: '', phone: '', address: '' };
          
          // Auto-close after success (optional)
          setTimeout(() => {
            if (this.message.includes("successfully")) {
              this.close();
            }
          }, 2000);
        } catch (e) {
          this.message = "Error adding customer. Please try again.";
        }
      },
      close() {
        this.$emit('close');
      }
    }
  };
  </script>