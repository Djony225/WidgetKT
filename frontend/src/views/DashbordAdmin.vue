<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useAdminStore, type User } from '@/stores/admin'
import { useAuthStore } from '@/stores/auth'
import { LayoutDashboard, Users, Server, Layers, LogOut, Shield, ShieldAlert, Trash2 } from 'lucide-vue-next'

const adminStore = useAdminStore()
const authStore = useAuthStore()

const currentUser = computed(() => authStore.user)

const toggleRole = async (user: User): Promise<void> => {

  const newRole = user.role === 'admin' ? 'user' : 'admin'
  const confirmation = confirm(`Voulez-vous changer le rôle de ${user.name} en ${newRole} ?`)

  if (confirmation) {
    await adminStore.updateUser(user.id, { role: newRole })
  }
}


const handleDeleteUser = async (user: User): Promise<void> => {

  const confirmation = confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur ${user.name} ? Cette action est irréversible.`)

  if (confirmation) {
    await adminStore.destroyUser(user.id)
  }
}

onMounted(async (): Promise<void> => {
     await authStore.fetchUser()

     await adminStore.fetchAdminData()
})

const logout = async (): Promise<void> => {
  await authStore.logout()
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex">
    <aside class="w-64 bg-slate-900 text-white p-6 flex flex-col fixed h-full">
      <div class="flex items-center gap-3 mb-10">
        <div class="bg-red-600 p-2 rounded-lg">
          <LayoutDashboard class="w-6 h-6" />
        </div>
        <span class="text-xl font-bold tracking-tight">KT Admin</span>
      </div>
      <nav class="flex-1 space-y-2">
        <button class="w-full flex items-center gap-3 px-4 py-3 bg-red-600 rounded-xl font-medium text-left">
          <LayoutDashboard class="w-5 h-5" /> Vue d'ensemble
        </button>
      </nav>
      <button @click="logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 mt-auto">
        <LogOut class="w-5 h-5" /> Déconnexion
      </button>
    </aside>

    <main class="flex-1 p-8 ml-64">
       <header class="flex justify-between items-center mb-10">
          <div class="flex items-center space-x-2 bg-white/50 p-1 pr-4 rounded-full border border-white">
            <div class="h-10 w-10 bg-gray-300 rounded-full overflow-hidden border-2 border-white">
              <img :src="`https://ui-avatars.com/api/?name=${authStore.user?.name || 'User'}&background=random`" alt="avatar" />
            </div>
            <span class="font-bold text-gray-800">{{ authStore.user?.name || 'Utilisateur' }}</span>
          </div>
          <div>
            <h1 class="text-center text-3xl font-bold text-slate-900">Tableau de Bord Administratif</h1>
            <p class="text-slate-500">Supervisez l'activité globale et gérez les droits des utilisateurs du système</p>
          </div>
       </header>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
          <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Utilisateurs inscrits</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ adminStore.totalUsers }}</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
            <Users class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
          <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Services Actifs</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ adminStore.totalServices }}</p>
          </div>
          <div class="bg-green-50 p-3 rounded-xl text-green-600">
            <Server class="w-6 h-6" />
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
          <div>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Widgets au Catalogue</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ adminStore.totalWidgets }}</p>
          </div>
          <div class="bg-purple-50 p-3 rounded-xl text-purple-600">
            <Layers class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
          <div>
            <h2 class="text-xl font-bold text-slate-900">Liste des Utilisateurs</h2>
            <p class="text-sm text-slate-400 mt-0.5">Modifiez les rôles ou révoquez l'accès aux comptes de la base de données</p>
          </div>
          <span class="bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1 rounded-full">
            {{ adminStore.users?.length || 0 }} Comptes trouvés
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                <th class="py-4 px-6">ID</th>
                <th class="py-4 px-6">Utilisateur</th>
                <th class="py-4 px-6">Email</th>
                <th class="py-4 px-6">Rôle actuel</th>
                <th class="py-4 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
              <tr v-for="user in adminStore.users" :key="user.id" class="hover:bg-slate-50/50 transition-colors" :class="{ 'bg-blue-50/20': currentUser && user.id === currentUser.id }">
                <td class="py-4 px-6 font-mono text-xs text-slate-400">#{{ user.id }}</td>
                <td class="py-4 px-6">
                  <div class="font-semibold text-slate-900 flex items-center gap-2">
                    {{ user.name }}
                    <span v-if="currentUser && user.id === currentUser.id" class="text-[10px] bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full">Vous</span>
                  </div>
                </td>
                <td class="py-4 px-6 text-slate-500">{{ user.email }}</td>

                <td class="py-4 px-6">
                  <span v-if="user.role === 'admin'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                    <Shield class="w-3.5 h-3.5" /> Administrateur
                  </span>
                  <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                    Utilisateur
                  </span>
                </td>

                <td class="py-4 px-6 text-right">
                  <div class="flex justify-end gap-2">
                    <button
                      @click="toggleRole(user)"
                      :disabled="currentUser ? user.id === currentUser.id : false"
                      title="Changer le rôle"
                      class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition-all disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                      <ShieldAlert class="w-4 h-4" />
                    </button>

                    <button
                      @click="handleDeleteUser(user)"
                      :disabled="currentUser ? user.id === currentUser.id : false "
                      title="Supprimer l'utilisateur"
                      class="p-2 text-slate-400 hover:text-red-600 rounded-xl hover:bg-red-50 transition-all disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!adminStore.users || adminStore.users.length === 0">
                <td colspan="5" class="py-12 text-center text-slate-400 font-medium">
                  Aucun utilisateur trouvé en base de données.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>
