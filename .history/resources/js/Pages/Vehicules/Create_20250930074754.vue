<template>
  <AppLayout title="Ajouter un véhicule">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter un nouveau véhicule
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
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

              <div>
                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">
                  Type de véhicule
                </label>
                <select
                  id="vehicule_type"
                  v-model="form.vehicule_type"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  required
                >
                  <option value="" disabled>Sélectionner...</option>
                  <option value="voiture">Voiture</option>
                  <option value="moto">Moto</option>
                  <option value="utilitaire">Utilitaire</option>
                  <option value="camion">Camion</option>
                  <option value="bus">Bus</option>
                  <option value="scooter">Scooter</option>
                  <option value="camionnette">Camionnette</option>
                </select>
                <p v-if="form.errors.vehicle_type" class="mt-2 text-sm text-red-600">
                  {{ form.errors.vehicle_type }}
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
                  help="Le numéro VIN se trouve généralement sur le tableau de bord côté conducteur"
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
                  <option value="">Sélectionner...</option>
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

            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <InformationCircleIcon class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-blue-800">
                    Information importante
                  </h3>
                  <div class="mt-2 text-sm text-blue-700">
                    <p>
                      Votre véhicule sera soumis à validation par notre équipe avant d'être activé.
                      Assurez-vous que toutes les informations sont correctes.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicules.index')"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <button
                type="submit"
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
                  Enregistrer
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'
import { InformationCircleIcon } from '@heroicons/vue/24/outline'

const form = useForm({
  make: '',
  model: '',
  year: new Date().getFullYear(),
  license_plate: '',
  vin: '',
  color: '',
  fuel_type: '',
  mileage: 0
})

const submit = () => {
  form.post(route('vehicules.store'), {
    onSuccess: () => {
      // Redirection handled by the controller
    }
  })
}
</script>
