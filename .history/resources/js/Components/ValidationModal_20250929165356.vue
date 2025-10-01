<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-10" @close="closeModal">
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
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
              <div>
                <div class="mt-3 text-center sm:mt-5">
                  <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900">
                    Validation du véhicule
                  </DialogTitle>
                  
                  <div v-if="vehicule" class="mt-4 text-left">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                      <h4 class="font-medium text-gray-900 mb-2">{{ vehicule.full_name }}</h4>
                      <div class="space-y-1 text-sm text-gray-600">
                        <p><span class="font-medium">Plaque:</span> {{ vehicule.license_plate }}</p>
                        <p><span class="font-medium">VIN:</span> {{ vehicule.vin }}</p>
                        <p><span class="font-medium">Année:</span> {{ vehicule.year }}</p>
                        <p><span class="font-medium">Propriétaire:</span> {{ vehicule.user?.name }}</p>
                      </div>
                    </div>

                    <form @submit.prevent="submitValidation">
                      <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Décision
                        </label>
                        <div class="space-y-2">
                          <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                 :class="form.action === 'validate' ? 'border-green-500 bg-green-50' : 'border-gray-300'">
                            <input
                              type="radio"
                              v-model="form.action"
                              value="validate"
                              class="h-4 w-4 text-green-600 focus:ring-green-500"
                            />
                            <span class="ml-3">
                              <span class="block text-sm font-medium text-gray-900">✅ Valider</span>
                              <span class="block text-xs text-gray-500">Le véhicule est conforme</span>
                            </span>
                          </label>

                          <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                 :class="form.action === 'correct' ? 'border-orange-500 bg-orange-50' : 'border-gray-300'">
                            <input
                              type="radio"
                              v-model="form.action"
                              value="correct"
                              class="h-4 w-4 text-orange-600 focus:ring-orange-500"
                            />
                            <span class="ml-3">
                              <span class="block text-sm font-medium text-gray-900">✏️ Demander des corrections</span>
                              <span class="block text-xs text-gray-500">Des informations doivent être corrigées</span>
                            </span>
                          </label>

                          <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                 :class="form.action === 'reject' ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                            <input
                              type="radio"
                              v-model="form.action"
                              value="reject"
                              class="h-4 w-4 text-red-600 focus:ring-red-500"
                            />
                            <span class="ml-3">
                              <span class="block text-sm font-medium text-gray-900">❌ Rejeter</span>
                              <span class="block text-xs text-gray-500">Le véhicule ne peut pas être accepté</span>
                            </span>
                          </label>
                        </div>
                        <p v-if="form.errors.action" class="mt-2 text-sm text-red-600">
                          {{ form.errors.action }}
                        </p>
                      </div>

                      <div v-if="form.action && form.action !== 'validate'" class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                          Notes / Raison <span class="text-red-500">*</span>
                        </label>
                        <textarea
                          id="notes"
                          v-model="form.notes"
                          rows="4"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          :placeholder="form.action === 'correct' 
                            ? 'Précisez les informations à corriger...' 
                            : 'Précisez la raison du rejet...'"
                          required
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                          Ces notes seront visibles par le propriétaire du véhicule
                        </p>
                        <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600">
                          {{ form.errors.notes }}
                        </p>
                      </div>

                      <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button
                          type="submit"
                          :disabled="form.processing || !form.action"
                          class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:col-start-2 disabled:opacity-50"
                          :class="getSubmitButtonClass()"
                        >
                          <span v-if="form.processing">Traitement...</span>
                          <span v-else>{{ getSubmitButtonText() }}</span>
                        </button>
                        <button
                          type="button"
                          @click="closeModal"
                          :disabled="form.processing"
                          class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0 disabled:opacity-50"
                        >
                          Annuler
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const props = defineProps({
  show: Boolean,
  vehicule: Object
})

const emit = defineEmits(['close', 'validated'])

const form = useForm({
  action: '',
  notes: ''
})

watch(() => props.show, (newVal) => {
  if (!newVal) {
    form.reset()
  }
})

const closeModal = () => {
  if (!form.processing) {
    emit('close')
  }
}

const submitValidation = () => {
  if (!props.vehicule) return

  form.post(route('vehicules.validate', props.vehicule.id), {
    onSuccess: () => {
      emit('validated')
      form.reset()
    },
    onError: () => {
      // Errors will be displayed in the form
    }
  })
}

const getSubmitButtonClass = () => {
  switch (form.action) {
    case 'validate':
      return 'bg-green-600 hover:bg-green-700 focus-visible:outline-green-600'
    case 'correct':
      return 'bg-orange-600 hover:bg-orange-700 focus-visible:outline-orange-600'
    case 'reject':
      return 'bg-red-600 hover:bg-red-700 focus-visible:outline-red-600'
    default:
      return 'bg-gray-600 hover:bg-gray-700 focus-visible:outline-gray-600'
  }
}

const getSubmitButtonText = () => {
  switch (form.action) {
    case 'validate':
      return 'Valider le véhicule'
    case 'correct':
      return 'Demander des corrections'
    case 'reject':
      return 'Rejeter le véhicule'
    default:
      return 'Confirmer'
  }
}
</script>