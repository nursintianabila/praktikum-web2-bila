<script setup>
import { useRoute } from 'vue-router'
import { safeTarget } from '../router'

const route = useRoute()
const target = safeTarget(route.query.redirect)
</script>

<template>
  <h1>Sesi Tidak Dapat Diperiksa</h1>

  <p>
    Sistem tidak dapat memeriksa sesi login saat ini.
  </p>

  <p>
    Periksa koneksi ke server, lalu coba lagi.
  </p>

  <RouterLink :to="target">
    Coba lagi
  </RouterLink>
</template>