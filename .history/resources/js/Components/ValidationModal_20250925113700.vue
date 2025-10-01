<!-- resources/js/Components/ValidationModal.vue -->
<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-50" @close="$emit('close')">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    <CheckCircleIcon class="h-6 w-6 text-blue-600" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                    <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900">
                      Validation du véhicule
                    </DialogTitle>
                    <div class="mt-4 space-y-4">
                      <div v-if="vehicule" class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900">{{ vehicule.full_name }}</h4>
                        <p class="text-sm text-gray-500">Plaque: {{ vehicule.license_plate }}</p>
                        <p class="text-sm text-gray-500">VIN: {{ vehicule.vin }}</p>
                        <p class="text-sm text-gray-500">Propriétaire: {{ vehicule.user?.name }}</p>
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Action à effectuer
                        </label>
                        <div class="space-y-2">
                          <label class="flex items-center">
                            <input
                              type="radio"
                              v-model="action"
                              value="validate"
                              class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700">Valider le véhicule</span>
                          </label>
                          <label class="flex items-center">
                            <input
                              type="radio"
                              v-model="action"
                              value="reject"
                              class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700">Rejeter le véhicule</span>
                          </label>
                        </div>
                      </div>

                      <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">
                          Notes (optionnel)
                        </label>
                        <textarea
                          id="notes"
                          v-model="notes"
                          rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          :placeholder="action === 'reject' ? 'Indiquez la raison du rejet...' : 'Notes de validation...'"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button
                  type="button"
                  @click="submitValidation"
                  :disabled="!action || processing"
                  :class="[
                    'inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto',
                    action === 'validate' 
                      ? 'bg-green-600 hover:bg-green-500 focus-visible:outline-green-600' 
                      : 'bg-red-600 hover:bg-red-500 focus-visible:outline-red-600',
                    (!action || processing) && 'opacity-50 cursor-not-allowed'
                  ]"
                >
                  <span v-if="processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Traitement...
                  </span>
                  <span v-else>
                    {{ action === 'validate' ? 'Valider' : 'Rejeter' }}
                  </span>
                </button>
                <button
                  type="button"
                  @click="$emit('close')"
                  :disabled="processing"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto disabled:opacity-50"
                >
                  Annuler
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  vehicule: Object
})

const emit = defineEmits(['close', 'validated'])

const action = ref('')
const notes = ref('')
const processing = ref(false)

const submitValidation = async () => {
  if (!action.value || !props.vehicule) return
  
  processing.value = true
  
  try {
    const form = useForm({
      action: action.value,
      notes: notes.value
    })
    
    await form.post(route('vehicules.validate', props.vehicule.id), {
      onSuccess: () => {
        emit('validated')
        resetForm()
      },
      onError: (errors) => {
        console.error('Validation failed:', errors)
      }
    })
  } finally {
    processing.value = false
  }
}

const resetForm = () => {
  action.value = ''
  notes.value = ''
}
</script>