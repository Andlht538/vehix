<!-- resources/js/Pages/Dashboard/Index.vue -->
<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard - {{ userRoleLabel }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <DashboardCard
            v-for="stat in statsCards"
            :key="stat.title"
            :title="stat.title"
            :value="stat.value"
            :icon="stat.icon"
            :color="stat.color"
            :trend="stat.trend"
            :trend-up="stat.trendUp"
          />
        </div>

        <!-- Client Dashboard -->
        <div v-if="userRole === 'client'" class="space-y-6">
          <!-- Recent Activities -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Activités récentes
              </h3>
              <div class="flow-root">
                <ul class="-mb-8">
                  <li v-for="(activity, index) in recentActivities" :key="index" class="relative pb-8">
                    <div v-if="index !== recentActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                    <div class="relative flex space-x-3">
                      <div>
                        <span :class="getActivityIcon(activity.type)">
                          <component :is="getActivityIconComponent(activity.type)" class="h-5 w-5 text-white" />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500">{{ activity.description }}</p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                          <time :datetime="activity.date">{{ formatDate(activity.date) }}</time>
                          <div v-if="activity.amount" class="font-medium text-gray-900">
                            {{ formatCurrency(activity.amount) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Upcoming Maintenances & Expiring Insurance -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upcoming Maintenances -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Maintenances à prévoir
                </h3>
                <div v-if="upcomingMaintenances.length" class="space-y-3">
                  <div
                    v-for="maintenance in upcomingMaintenances"
                    :key="maintenance.id"
                    class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ maintenance.vehicule.full_name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ maintenance.type }} - {{ formatDate(maintenance.next_maintenance_date) }}
                      </p>
                    </div>
                    <Link
                      :href="route('maintenances.show', maintenance.id)"
                      class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                    >
                      Voir
                    </Link>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500">
                  Aucune maintenance prévue
                </div>
              </div>
            </div>

            <!-- Expiring Insurance -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Assurances à renouveler
                </h3>
                <div v-if="expiringAssurances.length" class="space-y-3">
                  <div
                    v-for="assurance in expiringAssurances"
                    :key="assurance.id"
                    class="flex items-center justify-between p-3 bg-red-50 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ assurance.vehicule.full_name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ assurance.company }} - Expire le {{ formatDate(assurance.end_date) }}
                      </p>
                    </div>
                    <Link
                      :href="route('assurances.show', assurance.id)"
                      class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                    >
                      Voir
                    </Link>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500">
                  Aucune assurance à renouveler
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Validator Dashboard -->
        <div v-else-if="userRole === 'validator'" class="space-y-6">
          <!-- Pending Validations -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Véhicules en attente de validation
              </h3>
              <div v-if="pendingVehicules.length" class="space-y-4">
                <div
                  v-for="vehicule in pendingVehicules"
                  :key="vehicule.id"
                  class="border border-gray-200 rounded-lg p-4"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">
                        {{ vehicule.full_name }}
                      </h4>
                      <p class="text-sm text-gray-500">
                        Propriétaire: {{ vehicule.user.name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        VIN: {{ vehicule.vin }}
                      </p>
                    </div>
                    <div class="flex space-x-2">
                      <Link
                        :href="route('vehicules.show', vehicule.id)"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
                      >
                        Examiner
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-sm text-gray-500">
                Aucun véhicule en attente
              </div>
            </div>
          </div>
        </div>

        <!-- Admin Dashboard -->
        <div v-else-if="userRole === 'administrateur'" class="space-y-6">
          <!-- Recent Users -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Nouveaux utilisateurs
              </h3>
              <div class="space-y-3">
                <div
                  v-for="user in recentUsers"
                  :key="user.id"
                  class="flex items-center justify-between"
                >
                  <div class="flex items-center">
                    <img :src="user.profile_photo_url" :alt="user.name" class="h-10 w-10 rounded-full">
                    <div class="ml-4">
                      <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                      <p class="text-sm text-gray-500">{{ user.email }}</p>
                    </div>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ formatDate(user.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardCard from '@/Components/DashboardCard.vue'
import {
  TruckIcon,
  ShieldCheckIcon,
  WrenchScrewdriverIcon,
  BoltIcon,
  FireIcon,,
  MapPinIcon,
  UserGroupIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  user: Object,
  userRole: String,
  stats: Object,
  recentActivities: Array,
  upcomingMaintenances: Array,
  expiringAssurances: Array,
  pendingVehicules: Array,
  recentUsers: Array,
  recentValidations: Array,
  systemActivity: Object
})

const userRoleLabel = computed(() => {
  const labels = {
    'client': 'Client',
    'validator': 'Validateur',
    'administrateur': 'Administrateur'
  }
  return labels[props.userRole] || 'Utilisateur'
})

const statsCards = computed(() => {
  if (props.userRole === 'client') {
    return [
      {
        title: 'Véhicules',
        value: props.stats.totalVehicules,
        icon: TruckIcon,
        color: 'blue'
      },
      {
        title: 'Réparations',
        value: props.stats.totalReparations,
        icon: WrenchScrewdriverIcon,
        color: 'red'
      },
      {
        title: 'Maintenances',
        value: props.stats.totalMaintenances,
        icon: BoltIcon,
        color: 'yellow'
      },
      {
        title: 'Coût carburant ce mois',
        value: formatCurrency(props.stats.monthlyFuelCost),
        icon: FireIcon,
        color: 'green'
      }
    ]
  } else if (props.userRole === 'validator') {
    return [
      {
        title: 'En attente',
        value: props.stats.pendingValidations,
        icon: TruckIcon,
        color: 'yellow'
      },
      {
        title: 'Validés aujourd\'hui',
        value: props.stats.validatedToday,
        icon: CheckCircleIcon,
        color: 'green'
      },
      {
        title: 'Total validés',
        value: props.stats.totalValidated,
        icon: ShieldCheckIcon,
        color: 'blue'
      },
      {
        title: 'Rejetés aujourd\'hui',
        value: props.stats.rejectedToday,
        icon: WrenchScrewdriverIcon,
        color: 'red'
      }
    ]
  } else {
    return [
      {
        title: 'Utilisateurs',
        value: props.stats.totalUsers,
        icon: UserGroupIcon,
        color: 'blue'
      },
      {
        title: 'Véhicules',
        value: props.stats.totalVehicules,
        icon: TruckIcon,
        color: 'green'
      },
      {
        title: 'En attente',
        value: props.stats.pendingVehicules,
        icon: TruckIcon,
        color: 'yellow'
      },
      {
        title: 'Nouveaux ce mois',
        value: props.stats.newUsersThisMonth,
        icon: UserGroupIcon,
        color: 'purple'
      }
    ]
  }
})

const getActivityIcon = (type) => {
  const baseClass = 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
  const colors = {
    reparation: 'bg-red-500',
    maintenance: 'bg-yellow-500',
    ravitaillement: 'bg-green-500',
    default: 'bg-blue-500'
  }
  return `${baseClass} ${colors[type] || colors.default}`
}

const getActivityIconComponent = (type) => {
  const icons = {
    reparation: WrenchScrewdriverIcon,
    maintenance: BoltIcon,
    ravitaillement: FireIcon,
    default: TruckIcon
  }
  return icons[type] || icons.default
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}
</script>