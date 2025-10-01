<!-- resources/js/Components/VehiculeStatusBadge.vue -->
<template>
  <span :class="badgeClass">
    {{ status.label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: Object
})

const badgeClass = computed(() => {
  const baseClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
  
  switch (props.status.value) {
    case 'en_attente':
      return `${baseClass} bg-yellow-100 text-yellow-800`
    case 'valide':
      return `${baseClass} bg-green-100 text-green-800`
    case 'refuse':
      return `${baseClass} bg-red-100 text-red-800`
    default:
      return `${baseClass} bg-gray-100 text-gray-800`
  }
})
</script>