<template>
  <AppLayout title="Validation en attente">
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

        <div class="mx-auto h-12 w-12 text-yellow-600">
          <ExclamationTriangleIcon class="h-12 w-12" />
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Véhicule en attente de validation
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Votre véhicule est actuellement en cours de validation par notre équipe.
        </p>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <div class="space-y-6">
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

            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <ClockIcon class="h-5 w-5 text-yellow-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-yellow-800">
                    Validation en cours
                  </h3>
                  <div class="mt-2 text-sm text-yellow-700">
                    <p>
                      Votre véhicule a été soumis le {{ formatDate(vehicule.created_at) }}
                      et est actuellement en cours d'examen par notre équipe de validation.
                    </p>
                    <p class="mt-2">
                      Vous recevrez une notification dès que la validation sera terminée.
                      En attendant, vous pouvez consulter les détails de votre véhicule.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex space-x-4">
              <Link
                :href="route('vehicules.show', vehicule.id)"
                class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Voir les détails
              </Link>
              <Link
                :href="route('vehicules.edit', vehicule.id)"
                class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Modifier
              </Link>
            </div>

            <div class="text-center">
              <Link
                :href="route('vehicules.selection')"
                class="text-sm text-indigo-600 hover:text-indigo-500"
              >
                ← Retour à mes véhicules
              </Link>
            </div>

            <div class="text-center">
              <p class="text-xs text-gray-500">
                Si vous avez des questions, n'hésitez pas à nous contacter.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ExclamationTriangleIcon, ClockIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  vehicule: Object
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>