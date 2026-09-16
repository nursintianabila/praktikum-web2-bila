<script setup>
import { ref, onMounted } from 'vue'

const apiStatus = ref('Menghubungkan ke API...')

onMounted(async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/status')
    const data = await response.json()
    apiStatus.value = data.message
  } catch (error) {
    apiStatus.value = 'API tidak dapat diakses'
  }
})
</script>

<template>
  <main>
    <h1>Pemrograman Web 2</h1>
    <h2>Status Backend</h2>
    <p>{{ apiStatus }}</p>
  </main>
</template>