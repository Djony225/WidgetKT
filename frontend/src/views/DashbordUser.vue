<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { LayoutDashboard, Plus, Settings, LogOut, Trash2, X } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import { useDashboardStore } from '@/stores/dashboardWidgets'

import RedditWidget from '@/components/widgets/RedditWidget.vue'
import YoutubeWidget from '@/components/widgets/YoutubeWidget.vue'
import WeatherPredictionWidget from '@/components/widgets/WeatherPredictionWidget.vue'
import WeatherWidget from '@/components/widgets/WeatherWidget.vue'

interface WidgetParameter {
  id: number;
  widget_id: number;
  name: string;
  type: 'string' | 'integer';
}

interface Widget {
  id: number;
  name: string;
  description: string | null;
  service_id: number;
  parameters?: WidgetParameter[];
}

interface ConfiguredParam {
  id: number;
  dashboard_widget_id: number;
  param_name: string;
  param_value: string;
}

interface DashboardWidget {
  id: number;
  user_id: number;
  widget_id: number;
  refresh_rate: number;
  position: number;
  widget: Widget;
  configured_params?: ConfiguredParam[];
}

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const dashboardStore = useDashboardStore()

const isModalOpen = ref<boolean>(false)
const selectedWidget = ref<Widget | null>(null)

const widgetFormValues = ref<Record<string, string | number>>({})

const openAddWidgetModal = async (): Promise<void> => {
  isModalOpen.value = true
  selectedWidget.value = null
  widgetFormValues.value = {}
  await dashboardStore.fetchAvailableWidgets()
}

const selectWidgetForConfig = (widget: Widget): void => {
  selectedWidget.value = widget
  widgetFormValues.value = {}

  if (selectedWidget.value.parameters) {
    selectedWidget.value.parameters.forEach(param => {
      widgetFormValues.value[param.name] = param.type === 'integer' ? 5 : ''
    })
  }
}

const handleAddWidget = async (): Promise<void> => {
  if (!selectedWidget.value) return

  await dashboardStore.addWidgetToDashboard(selectedWidget.value.id, widgetFormValues.value)

  isModalOpen.value = false
  selectedWidget.value = null
  widgetFormValues.value = {}
}

const getParamValue = (userWidget: DashboardWidget, paramName: string, defaultValue: string = ''): string => {
  if (!userWidget.configured_params) return defaultValue
  const param = userWidget.configured_params.find((p: ConfiguredParam) => p.param_name === paramName)
  return param ? param.param_value : defaultValue
}

onMounted(async (): Promise<void> => {
   const token = route.query.token as string

   if (token) {
     localStorage.setItem('auth_token', token)
     await authStore.fetchUser()
     router.replace('/dashbord')
   } else {
     await authStore.fetchUser()
   }

   if (authStore.isLoggedIn) {
     await dashboardStore.fetchUserWidgets()
   }
})

const logout = async (): Promise<void> => {
  await authStore.logout()
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex">
    <aside class="w-64 bg-slate-900 text-white p-6 flex flex-col">
      <div class="flex items-center gap-3 mb-10">
        <div class="bg-blue-600 p-2 rounded-lg">
          <LayoutDashboard class="w-6 h-6" />
        </div>
        <span class="text-xl font-bold tracking-tight">WidgetKT</span>
      </div>

      <nav class="flex-1 space-y-2">
        <button class="w-full flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-xl font-medium">
          <LayoutDashboard class="w-5 h-5" /> Dashboard
        </button>
        <button class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white transition-colors">
          <Settings class="w-5 h-5" /> Paramètres
        </button>
      </nav>

      <button @click="logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 mt-auto">
        <LogOut class="w-5 h-5" /> Déconnexion
      </button>
    </aside>

    <main class="flex-1 p-8">
      <header class="flex justify-between items-center mb-10">
        <div class="flex items-center space-x-2 bg-white/50 p-1 pr-4 rounded-full border border-white">
          <div class="h-10 w-10 bg-gray-300 rounded-full overflow-hidden border-2 border-white">
            <img :src="`https://ui-avatars.com/api/?name=${authStore.user?.name || 'User'}&background=random`" alt="avatar" />
          </div>
          <span class="font-bold text-gray-800">{{ authStore.user?.name || 'Utilisateur' }}</span>
        </div>
        <div>
          <h1 class="text-center text-3xl font-bold text-slate-900">Mon Dashboard</h1>
          <p class="text-slate-500">Bienvenue sur votre espace personnalisé WidgetKT</p>
        </div>
        <button @click="openAddWidgetModal" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 hover:bg-slate-800 transition-all">
          <Plus class="w-5 h-5" /> Ajouter un Widget
        </button>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-if="dashboardStore.userWidgets.length === 0" class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-slate-300 text-slate-400">
          <p class="text-lg font-medium">Votre dashboard est vide</p>
          <p class="text-sm text-slate-400 mt-1">Cliquez sur le bouton en haut à droite pour ajouter un widget issu du catalogue.</p>
        </div>

        <template v-for="userWidget in (dashboardStore.userWidgets as DashboardWidget[])" :key="userWidget.id">
          <WeatherWidget
            v-if="userWidget.widget.name === 'city_temperature'"
            :widgetId="userWidget.id"  :initialCity="getParamValue(userWidget, 'city', 'Abidjan')"
            :refreshInterval="userWidget.refresh_rate"
          />

          <WeatherPredictionWidget
            v-else-if="userWidget.widget.name === 'weather_prediction'"
            :widgetId="userWidget.id"   :initialCity="getParamValue(userWidget, 'city', 'Abidjan')"
            :refreshInterval="userWidget.refresh_rate"
          />

          <RedditWidget
            v-else-if="userWidget.widget.name === 'reddit_posts'"
            :widgetId="userWidget.id"
            :subreddit="getParamValue(userWidget, 'subreddit', 'vuejs')"
            :limit="Number(getParamValue(userWidget, 'number', '5'))"
          />

          <YoutubeWidget
            v-else-if="userWidget.widget.name === 'video_views'"
            :widgetId="userWidget.id"
            :videoId="getParamValue(userWidget, 'video_id', 'dQw4w9WgXcQ')"
            :refreshInterval="userWidget.refresh_rate"
          />

          <div v-else class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
              <div class="flex justify-between items-start">
                <span class="px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-600 rounded-full uppercase tracking-wider">
                  {{ userWidget.widget.name.split('_')[0] }}
                </span>
                <button @click="dashboardStore.removeWidget(userWidget.id)" class="text-slate-300 hover:text-red-500 transition-colors">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
              <h3 class="text-lg font-bold text-slate-800 mt-2">{{ userWidget.widget.name.replace('_', ' ') }}</h3>
              <p class="text-sm text-slate-500 mt-1">{{ userWidget.widget.description }}</p>

              <div v-if="userWidget.configured_params?.length" class="mt-3 space-y-1 bg-slate-50 p-2 rounded-xl text-xs text-slate-600">
                <div v-for="p in userWidget.configured_params" :key="p.id">
                  <span class="font-semibold capitalize">{{ p.param_name.replace('_', ' ') }} :</span> {{ p.param_value }}
                </div>
              </div>
            </div>
            <div class="text-xs text-slate-400 pt-4 border-t border-slate-100 mt-4">
              ID d'instance : #{{ userWidget.id }}
            </div>
          </div>
        </template>
      </div>
    </main>

    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 transition-all">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-slate-900">
            {{ selectedWidget ? `Configurer ${selectedWidget.name.replace('_', ' ')}` : 'Choisir un widget' }}
          </h2>
          <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div v-if="!selectedWidget" class="space-y-3 max-h-96 overflow-y-auto pr-1">
          <div
            v-for="widget in (dashboardStore.availableWidgets as Widget[])"
            :key="widget.id"
            class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors border border-slate-100 cursor-pointer"
            @click="selectWidgetForConfig(widget)"
          >
            <div>
              <p class="font-semibold text-slate-800 capitalize">{{ widget.name.replace('_', ' ') }}</p>
              <p class="text-xs text-slate-500 mt-0.5">{{ widget.description || 'Pas de description.' }}</p>
            </div>
            <span class="text-blue-600 text-xs font-semibold bg-blue-50 px-2.5 py-1 rounded-full">Choisir</span>
          </div>
        </div>

        <div v-else class="space-y-4">
          <p class="text-sm text-slate-500">{{ selectedWidget.description }}</p>

          <div v-for="param in selectedWidget.parameters" :key="param.id" class="space-y-1">
            <label class="block text-sm font-semibold text-slate-700 capitalize">
              {{ param.name?.replace('_', ' ') }}
            </label>

            <input
              v-if="param.type === 'string'"
              v-model="widgetFormValues[param.name]"
              type="text"
              placeholder="Saisissez la valeur..."
              class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              required
            />

            <input
              v-else-if="param.type === 'integer'"
              v-model.number="widgetFormValues[param.name]"
              type="number"
              class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              required
            />
          </div>

          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="selectedWidget = null"
              class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-semibold text-sm transition-colors"
            >
              Retour
            </button>
            <button
              type="button"
              @click="handleAddWidget"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-semibold text-sm transition-colors"
            >
              Confirmer l'ajout
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
