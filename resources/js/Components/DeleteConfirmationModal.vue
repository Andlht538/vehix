<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-10" @close="handleClose">
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
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                  <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900">
                    {{ title || 'Confirmer la suppression' }}
                  </DialogTitle>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      {{ message || 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.' }}
                    </p>
                    
                    <!-- Item details if provided -->
                    <div v-if="item" class="mt-4 p-3 bg-gray-50 rounded-md">
                      <slot name="item-details" :item="item">
                        <p class="text-sm font-medium text-gray-900">
                          {{ item.name || item.full_name || 'Élément sélectionné' }}
                        </p>
                      </slot>
                    </div>

                    <!-- Confirmation input if required -->
                    <div v-if="requireConfirmation" class="mt-4">
                      <label for="confirm-text" class="block text-sm font-medium text-gray-700 mb-2">
                        Pour confirmer, tapez <span class="font-mono font-bold">{{ confirmationText }}</span>
                      </label>
                      <input
                        id="confirm-text"
                        v-model="confirmInput"
                        type="text"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                        :placeholder="confirmationText"
                      />
                    </div>

                    <!-- Warning message -->
                    <div v-if="warningMessage" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                      <div class="flex">
                        <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 flex-shrink-0" />
                        <p class="ml-3 text-sm text-yellow-700">
                          {{ warningMessage }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                  type="button"
                  :disabled="processing || (requireConfirmation && !isConfirmed)"
                  @click="handleDelete"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Suppression...
                  </span>
                  <span v-else>
                    {{ deleteButtonText || 'Supprimer' }}
                  </span>
                </button>
                <button
                  type="button"
                  :disabled="processing"
                  @click="handleClose"
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
import { ref, computed, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirmer la suppression'
  },
  message: {
    type: String,
    default: null
  },
  warningMessage: {
    type: String,
    default: null
  },
  item: {
    type: Object,
    default: null
  },
  deleteButtonText: {
    type: String,
    default: 'Supprimer'
  },
  requireConfirmation: {
    type: Boolean,
    default: false
  },
  confirmationText: {
    type: String,
    default: 'SUPPRIMER'
  }
})

const emit = defineEmits(['close', 'confirm'])

const processing = ref(false)
const confirmInput = ref('')

const isConfirmed = computed(() => {
  if (!props.requireConfirmation) return true
  return confirmInput.value === props.confirmationText
})

watch(() => props.show, (newVal) => {
  if (!newVal) {
    confirmInput.value = ''
    processing.value = false
  }
})

const handleClose = () => {
  if (!processing.value) {
    emit('close')
  }
}

const handleDelete = () => {
  if (processing.value || (props.requireConfirmation && !isConfirmed.value)) {
    return
  }
  
  processing.value = true
  emit('confirm')
}
</script>