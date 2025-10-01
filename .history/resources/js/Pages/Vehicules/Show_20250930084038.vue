<template>
  <AppLayout title="Détails du véhicule">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails du véhicule
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('vehicules.selection')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            v-if="canEdit"
            :href="route('vehicules.edit', vehicule.id)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Modifier
          </Link>
          <button
            v-if="canDelete"
            @click="confirmDelete"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
          >
            <TrashIcon class="h-4 w-4 mr-2" />
            Supprimer
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Status Alert -->
        <div v-if="vehicule.status !== 'valide'" class="mb-6">
          <div :class="getAlertClass(vehicule.status)" class="rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <component :is="getStatusIcon(vehicule.status)" class="h-5 w-5" :class="getAlertIconColor(vehicule.status)" />
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium" :class="getAlertTextColor(vehicule.status)">
                  {{ getStatusTitle(vehicule.status) }}
                </h3>
                <div class="mt-2 text-sm" :class="getAlertTextColor(vehicule.status)">
                  <p>{{ getStatusMessage(vehicule.status) }}</p>
                  <div v-if="vehicule.validation_notes" class="mt-3 p-3 bg-white rounded border">
                    <p class="font-medium text-gray-900">Notes du validateur:</p>
                    <p class="mt-1 text-gray-700">{{ vehicule.validation_notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Information -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Vehicle Details Card -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ vehicule.full_name }}
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Informations détaillées du véhicule
                  </p>
                </div>
                <VehiculeStatusBadge :status="vehicule.status" />
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Marque</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ vehicule.make }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Modèle</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ vehicule.model }}</dd>
                  </div>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Type de véhicule</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ getVehiculeTypeLabel(vehicule.vehicule_type) }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Année</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ vehicule.year }}</dd>
                  </div>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Plaque d'immatriculation</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ vehicule.license_plate }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Numéro VIN</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ vehicule.vin }}</dd>
                  </div>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Couleur</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ vehicule.color || 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Type de carburant</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ getFuelTypeLabel(vehicule.fuel_type) }}</dd>
                  </div>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Kilométrage</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatNumber(vehicule.mileage) }} km</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Propriétaire</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ vehicule.user?.name }}</dd>
                  </div>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date d'ajout</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(vehicule.created_at) }}</dd>
                  </div>
                  <div v-if="vehicule.validated_at" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Validé par</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      {{ vehicule.validator?.name }} le {{ formatDate(vehicule.validated_at) }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Validation Section (for validators/admins) -->
            <div v-if="canValidate && vehicule.status === 'en_attente'" class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Validation du véhicule
                </h3>
                <ValidationForm :vehicule="vehicule" @validated="handleValidation" />
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Statistiques rapides
                </h3>
                <dl class="space-y-3">
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Assurances</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ vehicule.assurances?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Réparations</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ vehicule.reparations?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Maintenances</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ vehicule.maintenances?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Ravitaillements</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ vehicule.ravitaillements?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Trajets</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ vehicule.trajets?.length || 0 }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Actions rapides
                </h3>
                <div class="space-y-3">
                  <Link
                    v-if="canEdit"
                    :href="route('vehicules.edit', vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <PencilIcon class="h-4 w-4 mr-2" />
                    Modifier
                  </Link>
                  <button
                    v-if="canDelete"
                    @click="confirmDelete"
                    class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                  >
                    <TrashIcon class="h-4 w-4 mr-2" />
                    Supprimer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ValidationForm from '@/Components/ValidationForm.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentDuplicateIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: Object,
  canValidate: Boolean
})

const canEdit = computed(() => {
  const statusValue = typeof props.vehicule.status === 'object' 
    ? props.vehicule.status.value 
    : props.vehicule.status
  return ['en_attente', 'refuse', 'a_corriger', 'doublon'].includes(statusValue)
})

const canDelete = computed(() => {
  return canEdit.value
})

const getStatusValue = (status) => {
  return typeof status === 'object' ? status.value : status
}

const getVehiculeTypeLabel = (type) => {
  const labels = {
    'voiture': 'Voiture',
    'moto': 'Moto',
    'utilitaire': 'Utilitaire',
    'camion': 'Camion',
    'bus': 'Bus',
    'scooter': 'Scooter',
    'camionnette': 'Camionnette'
  }
  return labels[type] || type || 'Non spécifié'
}

const getFuelTypeLabel = (type) => {
  const labels = {
    'essence': 'Essence',
    'diesel': 'Diesel',
    'hybride': 'Hybride',
    'electrique': 'Électrique',
    'gpl': 'GPL'
  }
  return labels[type] || type || 'Non spécifié'
}

const getStatusIcon = (status) => {
  const statusValue = getStatusValue(status)
  const icons = {
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return icons[statusValue] || ClockIcon
}

const getAlertClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'en_attente': 'bg-yellow-50 border border-yellow-200',
    'refuse': 'bg-red-50 border border-red-200',
    'a_corriger': 'bg-orange-50 border border-orange-200',
    'doublon': 'bg-purple-50 border border-purple-200'
  }
  return classes[statusValue] || 'bg-gray-50 border border-gray-200'
}

const getAlertIconColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-400',
    'refuse': 'text-red-400',
    'a_corriger': 'text-orange-400',
    'doublon': 'text-purple-400'
  }
  return colors[statusValue] || 'text-gray-400'
}

const getAlertTextColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-800',
    'refuse': 'text-red-800',
    'a_corriger': 'text-orange-800',
    'doublon': 'text-purple-800'
  }
  return colors[statusValue] || 'text-gray-800'
}

const getStatusTitle = (status) => {
  const statusValue = getStatusValue(status)
  const titles = {
    'en_attente': 'Véhicule en attente de validation',
    'refuse': 'Véhicule refusé',
    'a_corriger': 'Corrections requises',
    'doublon': 'Doublon détecté'
  }
  return titles[statusValue] || 'Information'
}

const getStatusMessage = (status) => {
  const statusValue = getStatusValue(status)
  const messages = {
    'en_attente': 'Ce véhicule est en cours de validation par notre équipe.',
    'refuse': 'Ce véhicule a été refusé. Consultez les notes ci-dessous.',
    'a_corriger': 'Des corrections sont nécessaires avant validation.',
    'doublon': 'Ce véhicule semble déjà exister dans le système.'
  }
  return messages[statusValue] || ''
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

const formatNumber = (num) => {
  return new Intl.NumberFormat('fr-FR').format(num)
}

const confirmDelete = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')) {
    router.delete(route('vehicules.destroy', props.vehicule.id), {
      onSuccess: () => {
        router.visit(route('vehicules.selection'))
      }
    })
  }
}

const handleValidation = () => {
  router.reload({ only: ['vehicule'] })
}
</script>