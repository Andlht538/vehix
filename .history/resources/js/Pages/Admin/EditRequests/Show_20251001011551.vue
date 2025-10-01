<template>
  <AppLayout title="Détails de la demande">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Demande de modification #{{ editRequest.id }}
        </h2>
        <Link
          :href="route('admin.edit-requests.index')"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
        >
          <ArrowLeftIcon class="h-4 w-4 mr-2" />
          Retour
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Statut -->
        <div class="bg-white shadow sm:rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Statut de la demande</h3>
              <p class="mt-1 text-sm text-gray-500">
                Demande créée le {{ formatDate(editRequest.created_at) }}
              </p>
            </div>
            <span :class="getStatusBadgeClass(editRequest.status)">
              <component 
                :is="editRequest.status === 'pending' ? ClockIcon : editRequest.status === 'approved' ? CheckCircleIcon : XCircleIcon" 
                class="h-5 w-5 mr-2" 
              />
              {{ getStatusLabel(editRequest.status) }}
            </span>
          </div>
        </div>

        <!-- Informations du véhicule -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Véhicule concerné</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500">Véhicule</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ editRequest.vehicule.full_name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Plaque</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ editRequest.vehicule.license_plate }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">VIN</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ editRequest.vehicule.vin }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Propriétaire</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ editRequest.user.name }}</dd>
              </div>
            </dl>
            <div class="mt-4">
              <Link
                :href="route('vehicules.show', editRequest.vehicule.id)"
                class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
              >
                Voir le véhicule complet →
              </Link>
            </div>
          </div>
        </div>

        <!-- Raison de la demande -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Raison de la demande</h3>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-700 whitespace-pre-line">{{ editRequest.reason }}</p>
            </div>
          </div>
        </div>

        <!-- Notes admin (si existent) -->
        <div v-if="editRequest.admin_notes" class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notes de l'administrateur</h3>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-700 whitespace-pre-line">{{ editRequest.admin_notes }}</p>
              <div v-if="editRequest.reviewer" class="mt-3 pt-3 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                  Par {{ editRequest.reviewer.name }} le {{ formatDate(editRequest.reviewed_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions (si en attente) -->
        <div v-if="editRequest.status === 'pending'" class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <!-- Approuver -->
              <button
                @click="showApproveModal = true"
                class="inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <CheckCircleIcon class="h-5 w-5 mr-2" />
                Approuver et modifier
              </button>

              <!-- Rejeter -->
              <button
                @click="showRejectModal = true"
                class="inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <XCircleIcon class="h-5 w-5 mr-2" />
                Rejeter la demande
              </button>
            </div>
          </div>
        </div>

        <!-- Action directe pour modifier (si approuvé) -->
        <div v-if="editRequest.status === 'approved'" class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Demande approuvée</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Vous pouvez maintenant modifier le véhicule
                </p>
              </div>
              <Link
                :href="route('vehicules.edit', editRequest.vehicule.id)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
              >
                <PencilSquareIcon class="h-5 w-5 mr-2" />
                Modifier le véhicule
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Approuver -->
    <div v-if="showApproveModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showApproveModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <CheckCircleIcon class="h-6 w-6 text-green-600" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Approuver la demande
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    En approuvant cette demande, vous serez redirigé vers la page de modification du véhicule pour effectuer les corrections nécessaires.
                  </p>
                  <div class="mt-4">
                    <label for="approve_notes" class="block text-sm font-medium text-gray-700">
                      Notes (optionnel)
                    </label>
                    <textarea
                      id="approve_notes"
                      v-model="approveForm.admin_notes"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                      placeholder="Ajoutez des notes si nécessaire..."
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="handleApprove"
              :disabled="approveForm.processing"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Approuver et modifier
            </button>
            <button
              type="button"
              @click="showApproveModal = false"
              :disabled="approveForm.processing"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Rejeter -->
    <div v-if="showRejectModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRejectModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <XCircleIcon class="h-6 w-6 text-red-600" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Rejeter la demande
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4">
                    Veuillez expliquer la raison du rejet au client.
                  </p>
                  <div>
                    <label for="reject_notes" class="block text-sm font-medium text-gray-700">
                      Raison du rejet <span class="text-red-500">*</span>
                    </label>
                    <textarea
                      id="reject_notes"
                      v-model="rejectForm.admin_notes"
                      rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                      placeholder="Exemple: Les informations fournies ne correspondent pas aux documents du véhicule..."
                      required
                    ></textarea>
                    <p v-if="rejectForm.errors.admin_notes" class="mt-2 text-sm text-red-600">
                      {{ rejectForm.errors.admin_notes }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="handleReject"
              :disabled="rejectForm.processing || !rejectForm.admin_notes"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Rejeter la demande
            </button>
            <button
              type="button"
              @click="showRejectModal = false"
              :disabled="rejectForm.processing"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { 
  ArrowLeftIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  PencilSquareIcon,
  ClockIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  editRequest: Object
})

const showApproveModal = ref(false)
const showRejectModal = ref(false)

const approveForm = useForm({
  admin_notes: ''
})

const rejectForm = useForm({
  admin_notes: ''
})

const handleApprove = () => {
  approveForm.post(route('admin.edit-requests.approve', props.editRequest.id), {
    onSuccess: () => {
      showApproveModal.value = false
      // Rediriger vers l'édition du véhicule
      router.visit(route('vehicules.edit', props.editRequest.vehicule.id))
    }
  })
}

const handleReject = () => {
  rejectForm.post(route('admin.edit-requests.reject', props.editRequest.id), {
    onSuccess: () => {
      showRejectModal.value = false
    }
  })
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

const getStatusBadgeClass = (status) => {
  const baseClass = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium'
  switch(status) {
    case 'pending':
      return `${baseClass} bg-yellow-100 text-yellow-800`
    case 'approved':
      return `${baseClass} bg-green-100 text-green-800`
    case 'rejected':
      return `${baseClass} bg-red-100 text-red-800`
    default:
      return `${baseClass} bg-gray-100 text-gray-800`
  }
}

const getStatusLabel = (status) => {
  switch(status) {
    case 'pending': return 'En attente'
    case 'approved': return 'Approuvée'
    case 'rejected': return 'Rejetée'
    default: return status
  }
}
</script>