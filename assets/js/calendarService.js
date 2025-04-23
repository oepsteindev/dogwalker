// // src/services/calendarService.js
// import axios from 'axios';

// const API_URL = '/api/calendar';

// export default {
//   // Event endpoints
//   getEvents(startDate, endDate) {
//     let url = `${API_URL}/events`;
//     if (startDate && endDate) {
//       url += `?start=${startDate.toISOString()}&end=${endDate.toISOString()}`;
//     }
//     return axios.get(url);
//   },

//   getEvent(id) {
//     return axios.get(`${API_URL}/events/${id}`);
//   },

//   createEvent(eventData) {
//     return axios.post(`${API_URL}/events`, eventData);
//   },

//   updateEvent(id, eventData) {
//     return axios.put(`${API_URL}/events/${id}`, eventData);
//   },

//   deleteEvent(id) {
//     return axios.delete(`${API_URL}/events/${id}`);
//   },

//   // Customer endpoints
//   getCustomers() {
//     return axios.get(`${API_URL}/customers`);
//   },

//   getCustomer(id) {
//     return axios.get(`${API_URL}/customers/${id}`);
//   },

//   createCustomer(customerData) {
//     return axios.post(`${API_URL}/customers`, customerData);
//   },

//   updateCustomer(id, customerData) {
//     return axios.put(`${API_URL}/customers/${id}`, customerData);
//   },

//   deleteCustomer(id) {
//     return axios.delete(`${API_URL}/customers/${id}`);
//   },

//   getCustomerEvents(id) {
//     return axios.get(`${API_URL}/customers/${id}/events`);
//   }
// };