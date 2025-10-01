<template>
  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
        Demandes de modification
      </h3>
      
      <div v-if="editRequests && editRequests.length > 0" class="space-y-4">
        <div 
          v-for="request in editRequests" 
          :key="request.id"
          class="border rounded-lg p-4"
          :class="{
            'border-yellow-200 bg-yellow-50': request.status === 'pending',
            'border-green-200 bg-green-50': request.status === 'approved',
            'border-red-200 bg-red-50': request.status === 'rejected'
          }"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-2 mb-2">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="{
                    'bg-yellow-100 text-yellow-800': request.status === 'pending',
                    'bg-green-100 text-green-800': request.status === 'approved',
                    'bg-red-100 text-red-800': request.status === 'rejected'
                  }"
                >
                  {{ getStatusLabel(request.status) }}
                </span>
                <span class="text-sm text-gray-500">
                  {{ formatDate(request.created_at) }}
                </span>
              </div>
              
              <p class="text-sm text-gray-700 mb-2">{{ request.reason }}</p>
              
              <div v-if="request.admin_notes" class="mt-3 p-3 bg-white rounded border">
                <p class="text-xs font-medium text-gray-900 mb-1">Notes de l'administrateur:</p>
                <p class="text-sm text-gray-700">{{ request.admin_notes }}</p>
                <p v-if="request.reviewer" class="text-xs text-gray-500 mt-2">
                  Par {{ request.reviewer.name }} le {{ formatDate(request.reviewed_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-6">
        <p class="text-sm text-gray-500">Aucune demande de modification</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  editRequests: Array
})

const getStatusLabel = (status) => {
  switch(status) {
    case 'pending': return 'En attente'
    case 'approved': return 'Approuvée'
    case 'rejected': return 'Rejetée'
    default: return status
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>