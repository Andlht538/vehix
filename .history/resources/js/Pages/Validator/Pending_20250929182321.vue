<template>
  <AppLayout title="Véhicules en attente">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Véhicules en attente de validation
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Filter -->
        <div class="mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <form @submit.prevent="search" class="flex gap-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher par marque, modèle, plaque, propriétaire..."
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                Rechercher
              </button>
              <Link
                v-if="filters.search"
                :href="route('validator.pending')"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
              >
                Réinitialiser
              </Link>
            </form>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-3 bg-yellow-100 rounded-full">
                <ClockIcon class="h-8 w-8 text-yellow-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm text-gray-600">En attente</p>
                <p class="text-2xl font-semibold text-gray-900">
                  {{ vehicules.total }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Vehicles List -->
        <div v-if="vehicules.data && vehicules.data.length > 0" class="bg-white shadow rounded-lg overflow-hidden">
          <div class="divide-y divide-gray-200">
            <div
              v-for="vehicule in vehicules.data"
              :key="vehicule.id"
              class="p-6 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <TruckIcon class="h-6 w-6 text-gray-400" />
                    <h3 class="text-lg font-medium text-gray-900">
                      {{ vehicule.full_name }}
                    </h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                      En attente
                    </span>
                  </div>

                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                      <p class="text-gray-500">Propriétaire</p>
                      <p class="font-medium text-gray-900">{{ vehicule.user?.name }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500">Plaque</p>
                      <p class="font-medium text-gray-900">{{ vehicule.license_plate }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500">VIN</p>
                      <p class="font-medium text-gray-900 text-xs">{{ vehicule.vin }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500">Année</p>
                      <p class="font-medium text-gray-900">{{ vehicule.year }}</p>
                    </div>
                  </div>

                  <div class="mt-3 text-sm text-gray-500">
                    <p>Soumis le {{ formatDate(vehicule.created_at) }}</p>
                  </div>
                </div>

                <div class="flex space-x-2 ml-4">
                  <Link
                    :href="route('vehicules.show', vehicule.id)"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <EyeIcon class="h-4 w-4 mr-2" />
                    Examiner
                  </Link>
                  <button
                    @click="openValidationModal(vehicule)"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    Valider
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="vehicules.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <Link
                  v-if="vehicules.prev_page_url"
                  :href="vehicules.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Précédent
                </Link>
                <Link
                  v-if="vehicules.next_page_url"
                  :href="vehicules.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Suivant
                </Link>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Affichage de
                    <span class="font-medium">{{ vehicules.from }}</span>
                    à
                    <span class="font-medium">{{ vehicules.to }}</span>
                    sur
                    <span class="font-medium">{{ vehicules.total }}</span>
                    résultats
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link
                      v-if="vehicules.prev_page_url"
                      :href="vehicules.prev_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Précédent</span>
                      ‹
                    </Link>
                    <Link
                      v-if="vehicules.next_page_url"
                      :href="vehicules.next_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Suivant</span>
                      ›
                    </Link>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white shadow rounded-lg p-12 text-center">
          <CheckCircleIcon class="h-16 w-16 text-gray-400 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            Aucun véhicule en attente
          </h3>
          <p class="text-gray-600">
            Tous les véhicules ont été traités. Bon travail !
          </p>
        </div>
      </div>
    </div>

    <!-- Validation Modal -->
    <ValidationModal
      :show="showValidationModal"
      :vehicule="selectedVehicule"
      @close="closeValidationModal"
      @validated="handleValidation"
    />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ValidationModal from '@/Components/ValidationModal.vue'
import {
  TruckIcon,
  ClockIcon,
  CheckCircleIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicules: Object,
  filters: Object
})

const searchQuery = ref(props.filters.search || '')
const showValidationModal = ref(false)
const selectedVehicule = ref(null)

const search = () => {
  router.get(route('validator.pending'), {
    search: searchQuery.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const openValidationModal = (vehicule) => {
  selectedVehicule.value = vehicule
  showValidationModal.value = true
}

const closeValidationModal = () => {
  showValidationModal.value = false
  selectedVehicule.value = null
}

const handleValidation = () => {
  closeValidationModal()
  router.reload({ only: ['vehicules'] })
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