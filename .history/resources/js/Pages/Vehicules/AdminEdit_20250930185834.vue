<template>
  <AppLayout title="Modifier le véhicule (Admin)">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <ShieldCheckIcon class="inline-block h-6 w-6 mr-2 text-indigo-600" />
          Modification Administrateur
        </h2>
        <Link
          :href="route('vehicules.show', vehicule.id)"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
        >
          <ArrowLeftIcon class="h-4 w-4 mr-2" />
          Retour
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <!-- Admin Warning -->
        <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-md p-4">
          <div class="flex">
            <ShieldCheckIcon class="h-5 w-5 text-indigo-400 flex-shrink-0" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-indigo-800">
                Mode Administrateur
              </h3>
              <div class="mt-2 text-sm text-indigo-700">
                <p>
                  Vous modifiez un véhicule validé en tant qu'administrateur. 
                  Les modifications seront appliquées immédiatement sans nouvelle validation.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Demandes en attente -->
        <div v-if="vehicule.edit_requests && vehicule.edit_requests.length > 0" class="mb-6">
          <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 flex-shrink-0" />
              <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-yellow-800">
                  Demandes de modification
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                  <div v-for="request in vehicule.edit_requests" :key="request.id" class="mt-2 p-3 bg-white rounded border border-yellow-200">
                    <p class="font-medium">{{ request.user?.name }} - {{ formatDate(request.created_at) }}</p>
                    <p class="mt-1 text-gray-700">{{ request.reason }}</p>
                    <span 
                      class="inline-flex items-center px-2 py-1 rounded text-xs font-medium mt-2"
                      :class="{
                        'bg-yellow-100 text-yellow-800': request.status === 'pending',
                        'bg-green-100 text-green-800': request.status === 'approved',
                        'bg-red-100 text-red-800': request.status === 'rejected'
                      }"
                    >
                      {{ request.status === 'pending' ? 'En attente' : request.status === 'approved' ? 'Approuvée' : 'Rejetée' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <FormInput
                id="make"
                v-model="form.make"
                label="Marque"
                placeholder="Ex: Peugeot, Renault..."
                required
                :error="form.errors.make"
              />

              <FormInput
                id="model"
                v-model="form.model"
                label="Modèle"
                placeholder="Ex: 308, Clio..."
                required
                :error="form.errors.model"
              />

              <!-- Champ pour Type véhicule -->
              <div>
                <label for="vehicule_type" class="block text-sm font-medium text-gray-700 mb-1">
                  Type véhicule
                </label>
                <select
                  id="vehicule_type"
                  v-model="form.vehicule_type"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  required
                >
                  <option value="" disabled>Sélectionner un type véhicule...</option>
                  <option value="voiture">Voiture</option>
                  <option value="moto">Moto</option>
                  <option value="utilitaire">Utilitaire</option>
                  <option value="camion">Camion</option>
                  <option value="bus">Bus</option>
                  <option value="scooter">Scooter</option>
                  <option value="camionnette">Camionnette</option>
                </select>
                <p v-if="form.errors.vehicule_type" class="mt-2 text-sm text-red-600">
                  {{ form.errors.vehicule_type }}
                </p>
              </div>

              <FormInput
                id="year"
                v-model="form.year"
                type="number"
                label="Année"
                :min="1900"
                :max="new Date().getFullYear() + 1"
                required
                :error="form.errors.year"
              />

              <FormInput
                id="license_plate"
                v-model="form.license_plate"
                label="Plaque d'immatriculation"
                placeholder="Ex: AB-123-CD"
                required
                :error="form.errors.license_plate"
              />

              <div class="sm:col-span-2">
                <FormInput
                  id="vin"
                  v-model="form.vin"
                  label="Numéro VIN"
                  placeholder="17 caractères alphanumériques"
                  maxlength="17"
                  required
                  :error="form.errors.vin"
                  help="Attention: La modification du VIN peut créer des problèmes de traçabilité"
                />
              </div>

              <FormInput
                id="color"
                v-model="form.color"
                label="Couleur"
                placeholder="Ex: Blanc, Noir..."
                :error="form.errors.color"
              />

              <div>
                <label for="fuel_type" class="block text-sm font-medium text-gray-700">
                  Type de carburant
                </label>
                <select
                  id="fuel_type"
                  v-model="form.fuel_type"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="" disabled>Sélectionner un type carburant...</option>
                  <option value="essence">Essence</option>
                  <option value="diesel">Diesel</option>
                  <option value="hybride">Hybride</option>
                  <option value="electrique">Électrique</option>
                  <option value="gpl">GPL</option>
                </select>
                <p v-if="form.errors.fuel_type" class="mt-2 text-sm text-red-600">
                  {{ form.errors.fuel_type }}
                </p>
              </div>

              <FormInput
                id="mileage"
                v-model="form.mileage"
                type="number"
                label="Kilométrage actuel"
                min="0"
                :error="form.errors.mileage"
              />
            </div>

            <div class="bg-red-50 border border-red-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">
                    Attention
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                      <li>Les modifications seront appliquées immédiatement</li>
                      <li>Le véhicule restera validé après modification</li>
                      <li>Aucune notification ne sera envoyée au propriétaire automatiquement</li>
                      <li>Assurez-vous de la précision des informations</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicules.show', vehicule.id)"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <button
                @click="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrement...
                </span>
                <span v-else>
                  <ShieldCheckIcon class="inline-block h-4 w-4 mr-2" />
                  Enregistrer (Admin)
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'
import { 
  ArrowLeftIcon, 
  ExclamationTriangleIcon,
  ShieldCheckIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: Object,
  isAdminEdit: Boolean
})

const form = useForm({
  make: props.vehicule.make,
  model: props.vehicule.model,
  vehicule_type: props.vehicule.vehicule_type,
  year: props.vehicule.year,
  license_plate: props.vehicule.license_plate,
  vin: props.vehicule.vin,
  color: props.vehicule.color,
  fuel_type: props.vehicule.fuel_type,
  mileage: props.vehicule.mileage
})

const submit = () => {
  form.put(route('vehicules.update', props.vehicule.id))
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>