<template>
  <form @submit.prevent="submitForm">
    <input v-model="form.name" placeholder="Name" required />
    <input v-model="form.email" placeholder="Email" required type="email" />
    <input v-model="form.phone" placeholder="Phone" />
    <input v-model="form.address" placeholder="Address" />
    <button type="submit">Add Customer</button>
    <div v-if="message">{{ message }}</div>
  </form>
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
        this.message = "Customer added!";
        this.form = { name: '', email: '', phone: '', address: '' };
      } catch (e) {
        this.message = "Error adding customer.";
      }
    },
    close() {
      this.$emit('close');
    }
  }
};
</script>