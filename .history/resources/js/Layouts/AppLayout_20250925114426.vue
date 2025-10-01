<!-- resources/js/Layouts/AppLayout.vue - Layout principal -->
<template>
  <div class="min-h-screen bg-gray-50" :class="{ 'offline': !isOnline }">
    <Disclosure as="nav" class="bg-white shadow-sm border-b border-gray-200" v-slot="{ open }">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <div class="flex flex-shrink-0 items-center">
              <Link :href="route('dashboard')" class="flex items-center">
                <TruckIcon class="h-8 w-8 text-indigo-600" />
                <span class="ml-2 text-xl font-bold text-gray-900">Gestion Auto</span>
              </Link>
            </div>
            
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link
                :href="route('dashboard')"
                :class="[
                  'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  route().current('dashboard') && 'border-indigo-500 text-gray-900',
                  'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium'
                ]"
              >
                Dashboard
              </Link>
              
              <Link
                v-if="$page.props.auth.user"
                :href="route('vehicules.index')"
                :class="[
                  'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  route().current('vehicules.*') && 'border-indigo-500 text-gray-900',
                  'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium'
                ]"
              >
                Véhicules
              </Link>

              <!-- Menu pour les clients avec véhicule validé -->
              <template v-if="hasValidatedVehicle">
                <Link
                  :href="route('assurances.index')"
                  :class="navLinkClass('assurances.*')"
                >
                  Assurances
                </Link>
                <Link
                  :href="route('reparations.index')"
                  :class="navLinkClass('reparations.*')"
                >
                  Réparations
                </Link>
                <Link
                  :href="route('maintenances.index')"
                  :class="navLinkClass('maintenances.*')"
                >
                  Maintenances
                </Link>
                <Link
                  :href="route('ravitaillements.index')"
                  :class="navLinkClass('ravitaillements.*')"
                >
                  Carburant
                </Link>
                <Link
                  :href="route('trajets.index')"
                  :class="navLinkClass('trajets.*')"
                >
                  Trajets
                </Link>
              </template>

              <!-- Menu admin -->
              <Link
                v-if="isAdmin"
                :href="route('administrateur.users.index')"
                :class="navLinkClass('administrateur.*')"
              >
                Administration
              </Link>
            </div>
          </div>

          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <!-- Indicateur de connexion -->
            <div class="mr-4">
              <span v-if="isOnline" class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                En ligne
              </span>
              <span v-else class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                <div class="w-2 h-2 bg-red-400 rounded-full mr-1"></div>
                Hors ligne
              </span>
            </div>

            <!-- Profile dropdown -->
            <Menu as="div" class="relative ml-3">
              <div>
                <MenuButton class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                  <span class="sr-only">Open user menu</span>
                  <img class="h-8 w-8 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                </MenuButton>
              </div>
              <transition enter-active-class="transition ease-out duration-200" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                  <MenuItem v-slot="{ active }">
                    <Link :href="route('profile.show')" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                      Profil
                    </Link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <form method="POST" @submit.prevent="logout">
                      <button type="submit" :class="[active ? 'bg-gray-100' : '', 'block w-full px-4 py-2 text-left text-sm text-gray-700']">
                        Déconnexion
                      </button>
                    </form>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>
          </div>

          <div class="-mr-2 flex items-center sm:hidden">
            <DisclosureButton class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset">
              <span class="sr-only">Open main menu</span>
              <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
              <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
          <!-- Mobile navigation items -->
          <DisclosureButton
            as="div"
            v-for="item in mobileNavItems"
            :key="item.name"
          >
            <Link
              :href="item.href"
              :class="[
                item.current ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800',
                'block border-l-4 py-2 pl-3 pr-4 text-base font-medium'
              ]"
            >
              {{ item.name }}
            </Link>
          </DisclosureButton>
        </div>
      </DisclosurePanel>
    </Disclosure>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <!-- Flash Messages -->
      <div v-if="$page.props.flash?.message" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
        <div class="rounded-md bg-green-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-5 w-5 text-green-400" />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">
                {{ $page.props.flash.message }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-500">
            © {{ new Date().getFullYear() }} Gestion Auto. Tous droits réservés.
          </p>
          <div class="flex space-x-4 text-sm text-gray-500">
            <span>Version 1.0</span>
            <span v-if="!isOnline" class="text-red-600">Mode hors ligne</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon, TruckIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

defineProps({
  title: String
})

const isOnline = ref(navigator.onLine)

const updateOnlineStatus = () => {
  isOnline.value = navigator.onLine
}

onMounted(() => {
  window.addEventListener('online', updateOnlineStatus)
  window.addEventListener('offline', updateOnlineStatus)
})

onUnmounted(() => {
  window.removeEventListener('online', updateOnlineStatus)
  window.removeEventListener('offline', updateOnlineStatus)
})

const isAdmin = computed(() => {
  return $page.props.auth.user?.role === 'administrateur'
})

const hasValidatedVehicle = computed(() => {
  // Cette valeur devrait être passée depuis le backend
  return $page.props.auth.user?.has_validated_vehicle || false
})

const navLinkClass = (routePattern) => {
  return [
    'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
    route().current(routePattern) && 'border-indigo-500 text-gray-900',
    'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium'
  ]
}

const mobileNavItems = computed(() => {
  const items = [
    { name: 'Dashboard', href: route('dashboard'), current: route().current('dashboard') },
    { name: 'Véhicules', href: route('vehicules.index'), current: route().current('vehicules.*') }
  ]
  
  if (hasValidatedVehicle.value) {
    items.push(
      { name: 'Assurances', href: route('assurances.index'), current: route().current('assurances.*') },
      { name: 'Réparations', href: route('reparations.index'), current: route().current('reparations.*') },
      { name: 'Maintenances', href: route('maintenances.index'), current: route().current('maintenances.*') },
      { name: 'Carburant', href: route('ravitaillements.index'), current: route().current('ravitaillements.*') },
      { name: 'Trajets', href: route('trajets.index'), current: route().current('trajets.*') }
    )
  }
  
  if (isAdmin.value) {
    items.push({ name: 'Administration', href: route('administrateur.users.index'), current: route().current('administrateur.*') })
  }
  
  return items
})

const logout = () => {
  router.post(route('logout'))
}
</script>

<style>
.offline {
  filter: grayscale(20%);
}

.offline::after {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #ef4444, #f97316, #eab308);
  z-index: 9999;
  animation: offline-pulse 2s ease-in-out infinite;
}

@keyframes offline-pulse {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}
</style>