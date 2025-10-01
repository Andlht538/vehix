<!-- resources/js/Components/DashboardCard.vue -->
<template>
  <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div :class="iconClass">
            <component :is="icon" class="h-6 w-6" />
          </div>
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd>
              <div class="text-lg font-medium text-gray-900">
                {{ value }}
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div v-if="trend" class="bg-gray-50 px-6 py-3">
      <div class="text-sm">
        <span :class="trendClass">
          {{ trend }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: Object,
  color: {
    type: String,
    default: 'blue'
  },
  trend: String,
  trendUp: {
    type: Boolean,
    default: true
  }
})

const iconClass = computed(() => {
  const colors = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    yellow: 'bg-yellow-500',
    red: 'bg-red-500',
    indigo: 'bg-indigo-500',
    purple: 'bg-purple-500',
    pink: 'bg-pink-500'
  }
  return `inline-flex items-center justify-center p-3 rounded-md text-white ${colors[props.color]}`
})

const trendClass = computed(() => {
  return props.trendUp 
    ? 'text-green-600 font-medium'
    : 'text-red-600 font-medium'
})
</script>