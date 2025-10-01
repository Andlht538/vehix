<template>
  <form @submit.prevent="submitValidation" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-3">
        DÃ©cision de validation
      </label>
      <div class="space-y-3">
        <!-- Option Valider -->
        <label
          class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50"
          :class="form.action === 'validate' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
        >
          <input
            type="radio"
            v-model="form.action"
            value="validate"
            class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500"
          />
          <span class="ml-3 flex-1">
            <span class="block text-sm font-semibold text-gray-900">
              âœ… Valider le vÃ©hicule
            </span>
            <span class="block text-xs text-gray-600 mt-1">
              Le vÃ©hicule est conforme et peut Ãªtre activÃ©
            </span>
          </span>
        </label>

        <!-- Option Ã€ corriger -->
        <label
          class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50"
          :class="form.action === 'correct' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'"
        >
          <input
            type="radio"
            v-model="form.action"
            value="correct"
            class="mt-1 h-4 w-4 text-orange-600 focus:ring-orange-500"
          />
          <span class="ml-3 flex-1">
            <span class="block text-sm font-semibold text-gray-900">
              âœï¸ Demander des corrections
            </span>
            <span class="block text-xs text-gray-600 mt-1">
              Des informations doivent Ãªtre corrigÃ©es par le propriÃ©taire
            </span>
          </span>
        </label>

        <!-- Option Rejeter -->
        <label
          class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50"
          :class="form.action === 'reject' ? 'border-red-500 bg-red-50' : 'border-gray-200'"
        >
          <input
            type="radio"
            v-model="form.action"
            value="reject"
            class="mt-1 h-4 w-4 text-red-600 focus:ring-red-500"
          />
          <span class="ml-3 flex-1">
            <span class="block text-sm font-semibold text-gray-900">
              âŒ Rejeter le vÃ©hicule
            </span>
            <span class="block text-xs text-gray-600 mt-1">
              Le vÃ©hicule ne peut pas Ãªtre acceptÃ© dans le systÃ¨me
            </span>
          </span>
        </label>
      </div>
      <p v-if="form.errors.action" class="mt-2 text-sm text-red-600">
        {{ form.errors.action }}
      </p>
    </div>

    <!-- Notes (obligatoires pour correction et rejet) -->
    <div v-if="form.action && form.action !== 'validate'">
      <label for="validation_notes" class="block text-sm font-medium text-gray-700 mb-2">
        Notes pour le propriÃ©taire <span class="text-red-500">*</span>
      </label>
      <textarea
        id="validation_notes"
        v-model="form.notes"
        rows="5"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        :placeholder="getPlaceholder()"
        required
      ></textarea>
      <p class="mt-2 text-xs text-gray-500">
        Ces notes seront visibles par le propriÃ©taire. Soyez prÃ©cis et constructif.
      </p>
      <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600">
        {{ form.errors.notes }}
      </p>
    </div>

    <!-- Notes optionnelles pour validation -->
    <div v-if="form.action === 'validate'">
      <label for="validation_notes_optional" class="block text-sm font-medium text-gray-700 mb-2">
        Notes (optionnel)
      </label>
      <textarea
        id="validation_notes_optional"
        v-model="form.notes"
        rows="3"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="Ajoutez des notes si nÃ©cessaire..."
      ></textarea>
      <p class="mt-2 text-xs text-gray-500">
        Vous pouvez ajouter des remarques pour le propriÃ©taire
      </p>
    </div>

    <!-- Boutons d'action -->
    <div class="flex justify-end space-x-3 pt-4 border-t">
      <button
        type="button"
        @click="resetForm"
        :disabled="form.processing"
        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
      >
        RÃ©initialiser
      </button>
      <button
        type="submit"
        :disabled="form.processing || !form.action"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50"
        :class="getSubmitButtonClass()"
      >
        <span v-if="form.processing" class="flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Traitement...
        </span>
        <span v-else>
          {{ getSubmitButtonText() }}
        </span>
      </button>
    </div>

    <!-- Message de confirmation -->
    <div v-if="showConfirmation" class="rounded-md bg-green-50 border border-green-200 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <CheckCircleIcon class="h-5 w-5 text-green-400" />
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-green-800">
            Validation effectuÃ©e avec succÃ¨s
          </p>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['validated'])

const showConfirmation = ref(false)

const form = useForm({
  action: '',
  notes: ''
})

// Reset form when vehicule changes
watch(() => props.vehicule, () => {
  form.reset()
  showConfirmation.value = false
})

const getPlaceholder = () => {
  switch (form.action) {
    case 'correct':
      return 'Exemple: "Veuillez corriger le numÃ©ro VIN. Le format semble incorrect (doit contenir 17 caractÃ¨res alphanumÃ©riques)."'
    case 'reject':
      return 'Exemple: "Le vÃ©hicule ne peut pas Ãªtre acceptÃ© car les informations fournies ne correspondent pas aux documents standards."'
    default:
      return 'Ajoutez vos notes ici...'
  }
}

const getSubmitButtonClass = () => {
  switch (form.action) {
    case 'validate':
      return 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
    case 'correct':
      return 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500'
    case 'reject':
      return 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    default:
      return 'bg-gray-400 cursor-not-allowed'
  }
}

const getSubmitButtonText = () => {
  switch (form.action) {
    case 'validate':
      return 'âœ… Valider le vÃ©hicule'
    case 'correct':
      return 'âœï¸ Demander corrections'
    case 'reject':
      return 'âŒ Rejeter le vÃ©hicule'
    default:
      return 'SÃ©lectionnez une action'
  }
}

const resetForm = () => {
  form.reset()
  showConfirmation.value = false
}

const submitValidation = () => {
  // Validation cÃ´tÃ© client
  if (!form.action) {
    form.errors.action = 'Veuillez sÃ©lectionner une action'
    return
  }

  if (form.action !== 'validate' && !form.notes?.trim()) {
    form.errors.notes = 'Les notes sont obligatoires pour cette action'
    return
  }

  // Soumettre au serveur
  form.post(route('validator.vehicules.validate', props.vehicule.id), {
    preserveScroll: true,
    onSuccess: () => {
      showConfirmation.value = true
      setTimeout(() => {
        showConfirmation.value = false
        emit('validated')
        // Recharger la page aprÃ¨s un court dÃ©lai
        setTimeout(() => {
          router.reload({ only: ['vehicule'] })
        }, 1500)
      }, 2000)
    },
    onError: (errors) => {
      console.error('Erreur de validation:', errors)
    }
  })
}
</script>