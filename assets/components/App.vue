<template>
  <div class="app">
    <div class="min-h-screen flex flex-col items-center justify-center gap-8">
    <Welcome />
    <SearchDogwalkers />
  </div>
  </div>
</template>

<script setup>
import Welcome from './Welcome.vue'
import SearchDogwalkers from './SearchDogwalkers.vue'
</script>
