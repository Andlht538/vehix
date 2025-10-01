<!-- resources/js/Components/SearchFilter.vue -->
<template>
  <div class="flex flex-col sm:flex-row gap-4 mb-6">
    <div class="flex-1">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
        </div>
        <input
          type="text"
          v-model="searchValue"
          @input="updateSearch"
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          :placeholder="placeholder"
        />
      </div>
    </div>
    <div v-if="filters" class="flex gap-2">
      <select
        v-for="filter in filters"
        :key="filter.name"
        v-model="filterValues[filter.name]"
        @change="updateFilters"
        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
      >
        <option value="">{{ filter.placeholder }}</option>
        <option 
          v-for="option in filter.options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

const props = defineProps({
  placeholder: {
    type: String,
    default: 'Rechercher...'
  },
  search: String,
  filters: Array,
  currentFilters: Object
})

const searchValue = ref(props.search || '')
const filterValues = reactive({...props.currentFilters})

const updateSearch = debounce(() => {
  router.get(route().current(), {
    ...route().params,
    search: searchValue.value,
    ...filterValues
  }, {
    preserveState: true,
    replace: true
  })
}, 300)

const updateFilters = () => {
  router.get(route().current(), {
    ...route().params,
    search: searchValue.value,
    ...filterValues
  }, {
    preserveState: true,
    replace: true
  })
}
</script>