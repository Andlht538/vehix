import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import SearchFilter from '@/Components/SearchFilter.vue'
import { ClockIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    AppLayout,
    DataTable,
    SearchFilter,
    Link,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon
  },
  props: {
    editRequests: Object,
    filters: Object
  },
  setup() {
    const columns = [
      { key: 'vehicule_info', label: 'Véhicule' },
      { key: 'client', label: 'Client' },
      { key: 'reason_preview', label: 'Raison' },
      { key: 'status', label: 'Statut' },
      { key: 'date', label: 'Date' }
    ]

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const getStatusIcon = (status) => {
      switch(status) {
        case 'pending': return 'ClockIcon'
        case 'approved': return 'CheckCircleIcon'
        case 'rejected': return 'XCircleIcon'
        default: return 'ClockIcon'
      }
    }

    const getStatusClass = (status) => {
      const baseClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
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

    return {
      columns,
      formatDate,
      getStatusIcon,
      getStatusClass,
      getStatusLabel
    }
  },
  template: `
    <AppLayout title="Demandes de modification">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Demandes de modification de véhicules
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <SearchFilter
            placeholder="Rechercher par véhicule..."
            :search="filters.search"
            :filters="[
              {
                name: 'status',
                placeholder: 'Tous les statuts',
                options: [
                  { value: 'pending', label: 'En attente' },
                  { value: 'approved', label: 'Approuvées' },
                  { value: 'rejected', label: 'Rejetées' }
                ]
              }
            ]"
            :current-filters="filters"
          />

          <DataTable
            title="Demandes de modification"
            :columns="columns"
            :data="editRequests"
          >
            <template #vehicule_info="{ item }">
              <div v-if="item?.vehicule" class="flex items-center">
                <div class="ml-0">
                  <div class="text-sm font-medium text-gray-900">
                    {{ item.vehicule.full_name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ item.vehicule.license_plate }}
                  </div>
                </div>
              </div>
            </template>

            <template #client="{ item }">
              <div v-if="item?.user" class="text-sm">
                <div class="font-medium text-gray-900">{{ item.user.name }}</div>
                <div class="text-gray-500">{{ item.user.email }}</div>
              </div>
            </template>

            <template #reason_preview="{ item }">
              <div class="text-sm text-gray-900 max-w-xs truncate">
                {{ item?.reason }}
              </div>
            </template>

            <template #status="{ item }">
              <span v-if="item" :class="getStatusClass(item.status)">
                <component :is="getStatusIcon(item.status)" class="h-4 w-4 mr-1" />
                {{ getStatusLabel(item.status) }}
              </span>
            </template>

            <template #date="{ item }">
              <div v-if="item" class="text-sm text-gray-900">
                {{ formatDate(item.created_at) }}
              </div>
            </template>

            <template #actions="{ item }">
              <Link
                v-if="item?.id"
                :href="route('admin.edit-requests.show', item.id)"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                {{ item.status === 'pending' ? 'Examiner' : 'Voir détails' }}
              </Link>
            </template>
          </DataTable>
        </div>
      </div>
    </AppLayout>
  `
}
