<template>
  <AppLayout title="Statut du véhicule">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Back Button -->
        <div class="mb-4">
          <Link
            :href="route('vehicules.selection')"
            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour à la sélection
          </Link>
        </div>

        <!-- Icon selon le statut -->
        <div class="mx-auto h-12 w-12" :class="getStatusIconColor(vehicule.status)">
          <component :is="getStatusIconComponent(vehicule.status)" class="h-12 w-12" />
        </div>
        
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          {{ getStatusTitle(vehicule.status) }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          {{ getStatusSubtitle(vehicule.status) }}
        </p>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <div class="space-y-6">
            <!-- Informations du véhicule -->
            <div class="text-center">
              <h3 class="text-lg font-medium text-gray-900">
                {{ vehicule.full_name }}
              </h3>
              <p class="mt-1 text-sm text-gray-500">
                Plaque: {{ vehicule.license_plate }}
              </p>
              <p class="text-sm text-gray-500">
                VIN: {{ vehicule.vin }}
              </p>
            </div>

            <!-- Message selon le statut -->
            <div :class="getMessageBoxClass(vehicule.status)">
              <div class="flex">
                <div class="flex-shrink-0">
                  <component :is="getStatusIconComponent(vehicule.status)" class="h-5 w-5" :class="getMessageIconColor(vehicule.status)" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium" :class="getMessageTextColor(vehicule.status)">
                    {{ getStatusMessageTitle(vehicule.status) }}
                  </h3>
                  <div class="mt-2 text-sm" :class="getMessageTextColor(vehicule.status)">
                    <p>{{ getStatusMessage(vehicule) }}</p>
                    
                    <!-- Notes de validation si présentes -->
                    <div v-if="vehicule.validation_notes" class="mt-3 p-3 bg-white rounded border">
                      <p class="font-medium text-gray-900">Notes du validateur:</p>
                      <p class="mt-1 text-gray-700">{{ vehicule.validation_notes }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions selon le statut -->
            <div class="space-y-3">
              <!-- En attente -->
              <template v-if="vehicule.status === 'en_attente'">
                <Link
                  :href="route('vehicules.show', vehicule.id)"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  Voir les détails
                </Link>
                <Link
                  :href="route('vehicules.edit', vehicule.id)"
                  class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Modifier
                </Link>
              </template>

              <!-- Refusé ou À corriger -->
              <template v-if="['refuse', 'a_corriger'].includes(vehicule.status)">
                <Link
                  :href="route('vehicules.edit', vehicule.id)"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  Corriger les informations
                </Link>
                <Link
                  :href="route('vehicules.show', vehicule.id)"
                  class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Voir les détails
                </Link>
              </template>

              <!-- Doublon -->
              <template v-if="vehicule.status === 'doublon'">
                <Link
                  :href="route('vehicules.show', vehicule.id)"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  Voir les détails
                </Link>
                <button
                  @click="confirmDelete"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  Supprimer ce véhicule
                </button>
              </template>
            </div>

            <div class="text-center">
              <Link
                :href="route('vehicules.selection')"
                class="text-sm text-indigo-600 hover:text-indigo-500"
              >
                ← Retour à mes véhicules
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { 
  ExclamationTriangleIcon, 
  ClockIcon, 
  ArrowLeftIcon,
  XCircleIcon,
  DocumentDuplicateIcon
} from '@heroicons/vue/24/outline'

defineProps({
  vehicule: Object
})

const getStatusIconComponent = (status) => {
  const icons = {
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return icons[status] || ClockIcon
}

const getStatusIconColor = (status) => {
  const colors = {
    'en_attente': 'text-yellow-600',
    'refuse': 'text-red-600',
    'a_corriger': 'text-orange-600',
    'doublon': 'text-purple-600'
  }
  return colors[status] || 'text-gray-600'
}

const getStatusTitle = (status) => {
  const titles = {
    'en_attente': 'Véhicule en attente de validation',
    'refuse': 'Véhicule refusé',
    'a_corriger': 'Corrections requises',
    'doublon': 'Doublon détecté'
  }
  return titles[status] || 'Statut du véhicule'
}

const getStatusSubtitle = (status) => {
  const subtitles = {
    'en_attente': 'Votre véhicule est en cours de validation par notre équipe.',
    'refuse': 'Votre véhicule a été refusé. Veuillez consulter les notes ci-dessous.',
    'a_corriger': 'Des corrections sont nécessaires avant la validation.',
    'doublon': 'Ce véhicule semble déjà exister dans notre système.'
  }
  return subtitles[status] || ''
}

const getMessageBoxClass = (status) => {
  const classes = {
    'en_attente': 'bg-yellow-50 border border-yellow-200 rounded-md p-4',
    'refuse': 'bg-red-50 border border-red-200 rounded-md p-4',
    'a_corriger': 'bg-orange-50 border border-orange-200 rounded-md p-4',
    'doublon': 'bg-purple-50 border border-purple-200 rounded-md p-4'
  }
  return classes[status] || 'bg-gray-50 border border-gray-200 rounded-md p-4'
}

const getMessageIconColor = (status) => {
  const colors = {
    'en_attente': 'text-yellow-400',
    'refuse': 'text-red-400',
    'a_corriger': 'text-orange-400',
    'doublon': 'text-purple-400'
  }
  return colors[status] || 'text-gray-400'
}

const getMessageTextColor = (status) => {
  const colors = {
    'en_attente': 'text-yellow-800',
    'refuse': 'text-red-800',
    'a_corriger': 'text-orange-800',
    'doublon': 'text-purple-800'
  }
  return colors[status] || 'text-gray-800'
}

const getStatusMessageTitle = (status) => {
  const titles = {
    'en_attente': 'Validation en cours',
    'refuse': 'Véhicule refusé',
    'a_corriger': 'Corrections nécessaires',
    'doublon': 'Doublon détecté'
  }
  return titles[status] || 'Information'
}

const getStatusMessage = (vehicule) => {
  const messages = {
    'en_attente': `Votre véhicule a été soumis le ${formatDate(vehicule.created_at)} et est actuellement en cours d'examen par notre équipe de validation. Vous recevrez une notification dès que la validation sera terminée.`,
    'refuse': 'Votre véhicule a été refusé par le validateur. Veuillez consulter les notes ci-dessous et apporter les corrections nécessaires.',
    'a_corriger': 'Le validateur a identifié des éléments à corriger. Veuillez consulter les notes et modifier votre véhicule en conséquence.',
    'doublon': 'Ce véhicule (VIN) existe déjà dans notre système. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez nous contacter. Sinon, vous pouvez supprimer ce doublon.'
  }
  return messages[vehicule.status] || ''
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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
</script>