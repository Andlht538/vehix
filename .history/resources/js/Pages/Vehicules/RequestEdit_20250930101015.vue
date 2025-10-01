import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'
import { ArrowLeftIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    AppLayout,
    FormInput,
    Link,
    ArrowLeftIcon,
    ExclamationTriangleIcon
  },
  props: {
    vehicule: Object
  },
  setup(props) {
    const form = useForm({
      reason: '',
      requested_changes: {}
    })

    const submit = () => {
      form.post(route('vehicules.request-edit.store', props.vehicule.id))
    }

    return { form, submit }
  },
  template: `
    <AppLayout title="Demander une modification">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Demander une modification
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
          <!-- Warning Alert -->
          <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 flex-shrink-0" />
              <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                  Demande de modification d'un véhicule validé
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                  <p>
                    Votre véhicule est actuellement validé et actif. Si vous avez détecté une erreur de frappe
                    ou une information incorrecte, vous pouvez soumettre une demande de modification.
                    Un administrateur examinera votre demande et pourra effectuer les corrections nécessaires.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Véhicule Info -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">{{ vehicule.full_name }}</h3>
              <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                  <span class="font-medium">Plaque:</span> {{ vehicule.license_plate }}
                </div>
                <div>
                  <span class="font-medium">VIN:</span> {{ vehicule.vin }}
                </div>
              </div>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-6">
              <!-- Raison de la demande -->
              <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                  Décrivez l'erreur ou la modification nécessaire <span class="text-red-500">*</span>
                </label>
                <textarea
                  id="reason"
                  v-model="form.reason"
                  rows="6"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Exemple: Le kilométrage actuel est incorrect. Il devrait être 85000 km au lieu de 58000 km. Erreur de saisie lors de l'enregistrement initial."
                  required
                ></textarea>
                <p class="mt-2 text-xs text-gray-500">
                  Soyez le plus précis possible. Indiquez quelle information est incorrecte et quelle devrait être la valeur correcte.
                </p>
                <p v-if="form.errors.reason" class="mt-2 text-sm text-red-600">
                  {{ form.errors.reason }}
                </p>
              </div>

              <!-- Informations importantes -->
              <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                      À savoir
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                      <ul class="list-disc list-inside space-y-1">
                        <li>Votre demande sera examinée par un administrateur</li>
                        <li>Vous serez notifié de la décision</li>
                        <li>Une seule demande en attente est autorisée par véhicule</li>
                        <li>Les modifications approuvées seront effectuées par l'administrateur</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Boutons d'action -->
              <div class="flex justify-end space-x-3 pt-4 border-t">
                <Link
                  :href="route('vehicules.show', vehicule.id)"
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
                    Envoi en cours...
                  </span>
                  <span v-else>
                    Soumettre la demande
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  `
}