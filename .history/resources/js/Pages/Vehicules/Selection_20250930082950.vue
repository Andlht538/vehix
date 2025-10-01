<template>
  <AppLayout title="Mes Véhicules">
    <div class="min-h-screen bg-gray-50 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Mes Véhicules</h1>
          <p class="mt-2 text-sm text-gray-600">
            Sélectionnez un véhicule validé pour accéder au tableau de bord
          </p>
        </div>

        <!-- Vehicule Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Existing Vehicule Cards -->
          <div
            v-for="vehicule in vehicules"
            :key="vehicule.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 cursor-pointer"
            @click="handleCardClick(vehicule)"
            >
            <!-- Card Header with Status -->
            <div :class="getHeaderClass(vehicule.status)" class="px-6 py-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <TruckIcon class="h-8 w-8 text-white" />
                  <div>
                    <h3 class="text-lg font-semibold text-white">
                      {{ vehicule.make }} - {{ vehicule.model }}
                    </h3>
                    <p class="text-sm text-white opacity-90">
                      {{ vehicule.license_plate }} • {{ getVehiculeTypeLabel(vehicule.vehicule_type) }}
                    </p>
                  </div>
                </div>
                <component :is="getStatusIcon(vehicule.status)" class="h-6 w-6 text-white" />
              </div>
            </div>


            <!-- Card Body -->
            <div class="px-6 py-4">
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Année:</span>
                  <span class="font-medium text-gray-900">{{ vehicule.year }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">VIN:</span>
                  <span class="font-medium text-gray-900 text-xs">{{ vehicule.vin }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Statut:</span>
                  <span :class="getStatusBadgeClass(vehicule.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                    {{ getStatusLabel(vehicule.status) }}
                  </span>
                </div>
              </div>

              <!-- Status Messages -->
              <div v-if="vehicule.status !== 'valide'" class="mt-4 p-3 rounded-md" :class="getMessageClass(vehicule.status)">
                <p class="text-sm" :class="getMessageTextClass(vehicule.status)">
                  {{ getStatusMessage(vehicule) }}
                </p>
              </div>

              <!-- Action Buttons -->
                <div class="mt-6 space-y-2" @click.stop>
                <!-- Validated Vehicule - Access Dashboard -->
                <button
                    v-if="getStatusValue(vehicule.status) === 'valide'"
                    @click="selectVehicule(vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    <ArrowRightIcon class="h-5 w-5 mr-2" />
                    Accéder au tableau de bord
                </button>

                <!-- Pending Vehicule - View Details -->
                <Link
                    v-if="getStatusValue(vehicule.status) === 'en_attente'"
                    :href="route('vehicules.pending-detail', vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors"
                >
                    <ClockIcon class="h-5 w-5 mr-2" />
                    Voir le statut
                </Link>

                <!-- Rejected/To Correct/Duplicate - Edit -->
                <Link
                    v-if="['refuse', 'a_corriger', 'doublon'].includes(getStatusValue(vehicule.status))"
                    :href="route('vehicules.edit', vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                >
                    <PencilIcon class="h-5 w-5 mr-2" />
                    Corriger les informations
                </Link>

                <!-- Secondary Action: View Details -->
                <Link
                    :href="route('vehicules.show', vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                >
                    Voir les détails
                </Link>
                </div>
            </div>
          </div>

          <!-- Add New Vehicule Card -->
        <Link
        :href="route('vehicules.create')"
        class="w-full h-full bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 border-2 border-dashed border-gray-300 hover:border-indigo-400 flex items-center justify-center min-h-[400px] cursor-pointer group"
        >
        <div class="text-center p-6">
            <PlusCircleIcon class="h-16 w-16 text-gray-400 group-hover:text-indigo-600 mx-auto mb-4 transition-colors" />
            <h3 class="text-lg font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors">
            Ajouter un véhicule
            </h3>
            <p class="text-sm text-gray-500 mt-2">
            Cliquez pour enregistrer un nouveau véhicule
            </p>
        </div>
        </Link>
        </div>

        <!-- Empty State -->
        <div v-if="!vehicules || vehicules.length === 0" class="text-center py-12">
        <TruckIcon class="h-16 w-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Aucun véhicule enregistré
        </h3>
        <p class="text-gray-600 mb-6">
            Commencez par ajouter votre premier véhicule
        </p>
        <Link
            :href="route('vehicules.create')"
            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
        >
            <PlusCircleIcon class="h-5 w-5 mr-2" />
            Ajouter un véhicule
        </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  TruckIcon,
  PlusCircleIcon,
  CheckCircleIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  ArrowRightIcon,
  PencilIcon,
  DocumentDuplicateIcon
} from '@heroicons/vue/24/outline'

defineProps({
  vehicules: Array
})

// Fonction pour convertir le type de véhicule en label lisible
const getVehiculeTypeLabel = (vehicleType) => {
  const labels = {
    'voiture': 'Voiture',
    'moto': 'Moto',
    'utilitaire': 'Utilitaire',
    'camion': 'Camion',
    'bus': 'Bus',
    'scooter': 'Scooter',
    'camionnette': 'Camionnette'
  }
  return labels[vehicleType] || vehiculeType || 'Inconnu'
}

const handleCardClick = (vehicule) => {
  // Extraire la valeur du statut (support pour objet ou string)
  const statusValue = typeof vehicule.status === 'object' ? vehicule.status.value : vehicule.status
  
  console.log('Clic sur véhicule:', vehicule.id, 'Statut:', statusValue)
  
  if (statusValue === 'valide') {
    selectVehicule(vehicule.id)
  } else {
    router.visit(route('vehicules.pending-detail', vehicule.id))
  }
}

const selectVehicule = (vehiculeId) => {
  router.post(route('vehicules.select', vehiculeId))
}

// Fonction helper pour extraire la valeur du statut
const getStatusValue = (status) => {
  return typeof status === 'object' ? status.value : status
}
const getHeaderClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'valide': 'bg-gradient-to-r from-green-500 to-green-600',
    'en_attente': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
    'refuse': 'bg-gradient-to-r from-red-500 to-red-600',
    'a_corriger': 'bg-gradient-to-r from-orange-500 to-orange-600',
    'doublon': 'bg-gradient-to-r from-purple-500 to-purple-600'
  }
  return classes[statusValue] || 'bg-gradient-to-r from-gray-500 to-gray-600'
}

const getStatusIcon = (status) => {
  const statusValue = getStatusValue(status)
  const icons = {
    'valide': CheckCircleIcon,
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return icons[statusValue] || ClockIcon
}

const getStatusLabel = (status) => {
  const statusValue = getStatusValue(status)
  const labels = {
    'valide': 'Validé',
    'en_attente': 'En attente',
    'refuse': 'Refusé',
    'a_corriger': 'À corriger',
    'doublon': 'Doublon détecté'
  }
  return labels[statusValue] || 'Inconnu'
}
const getStatusBadgeClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'valide': 'bg-green-100 text-green-800',
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'refuse': 'bg-red-100 text-red-800',
    'a_corriger': 'bg-orange-100 text-orange-800',
    'doublon': 'bg-purple-100 text-purple-800'
  }
  return classes[statusValue] || 'bg-gray-100 text-gray-800'
}

const getMessageClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'en_attente': 'bg-yellow-50 border border-yellow-200',
    'refuse': 'bg-red-50 border border-red-200',
    'a_corriger': 'bg-orange-50 border border-orange-200',
    'doublon': 'bg-purple-50 border border-purple-200'
  }
  return classes[statusValue] || 'bg-gray-50 border border-gray-200'
}

const getMessageTextClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'en_attente': 'text-yellow-800',
    'refuse': 'text-red-800',
    'a_corriger': 'text-orange-800',
    'doublon': 'text-purple-800'
  }
  return classes[statusValue] || 'text-gray-800'
}

const getStatusMessage = (vehicule) => {
  const statusValue = getStatusValue(vehicule.status)
  const messages = {
    'en_attente': 'Votre véhicule est en cours de validation par notre équipe.',
    'refuse': vehicule.validation_notes || 'Ce véhicule a été refusé. Veuillez corriger les informations.',
    'a_corriger': vehicule.validation_notes || 'Des corrections sont nécessaires avant validation.',
    'doublon': vehicule.validation_notes || 'Ce véhicule semble déjà exister dans notre système.'
  }
  return messages[statusValue] || 'Statut inconnu'
}
</script>
