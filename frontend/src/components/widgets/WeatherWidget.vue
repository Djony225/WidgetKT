<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { CloudSun, RefreshCw, MapPin, Trash2 } from 'lucide-vue-next'
import axios from 'axios'
import { useDashboardStore } from '@/stores/dashboardWidgets'

interface WeatherData {
  name: string;
  main: {
    temp: number;
  };
  weather: Array<{
    description: string;
  }>;
}

const props = defineProps<{
  widgetId: number
  initialCity: string
  refreshInterval: number
}>()

const dashboardStore = useDashboardStore()

const weatherData = ref<WeatherData | null>(null)
const loading = ref(true)
const lastUpdate = ref(new Date())

let timer: ReturnType<typeof setInterval> | null = null

const API_KEY = "44132efecb4498db65d12b831ba098c0"

const fetchWeather = async () => {
  loading.value = true
  try {
    const response = await axios.get(
      `https://api.openweathermap.org/data/2.5/weather?q=${props.initialCity}&units=metric&appid=${API_KEY}`
    )
    weatherData.value = response.data
    lastUpdate.value = new Date()
  } catch (error) {
    console.error("Erreur météo:", error)
  } finally {
    loading.value = false
  }
}


const deleteWidget = async (): Promise<void> => {
  if (confirm(`Voulez-vous vraiment supprimer le widget météo de ${props.initialCity} ?`)) {
    await dashboardStore.removeWidget(props.widgetId)
  }
}

onMounted(() => {
  fetchWeather()
  timer = setInterval(fetchWeather, props.refreshInterval * 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group">
    <div class="flex justify-between items-start mb-6">
      <div class="bg-orange-100 p-3 rounded-2xl text-orange-600">
        <CloudSun class="w-6 h-6" />
      </div>
      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          @click="fetchWeather"
          :disabled="loading"
          class="p-2 text-slate-400 hover:text-blue-600 transition-colors disabled:opacity-50"
          title="Rafraîchir les données"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>

        <button
          @click="deleteWidget"
          class="p-2 text-slate-400 hover:text-red-600 transition-colors"
          title="Supprimer le widget"
        >
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </div>

    <div v-if="loading && !weatherData" class="animate-pulse space-y-3">
      <div class="h-8 bg-slate-100 rounded w-1/2"></div>
      <div class="h-4 bg-slate-100 rounded w-3/4"></div>
    </div>

    <div v-else-if="weatherData">
      <div class="flex items-center gap-2 text-slate-500 mb-1">
        <MapPin class="w-4 h-4" />
        <span class="text-sm font-medium uppercase tracking-wider">{{ weatherData.name }}</span>
      </div>

      <div class="text-4xl font-black text-slate-900 mb-4">
        {{ Math.round(weatherData.main.temp) }}°C
      </div>

      <p class="text-slate-500 text-sm capitalize">
        {{ weatherData.weather?.[0]?.description }}
      </p>
    </div>

    <div class="mt-6 pt-4 border-t border-slate-50 text-[10px] text-slate-400 flex justify-between">
      <span>Rafraîchissement: {{ refreshInterval }}s</span>
      <span>Mis à jour à {{ lastUpdate.toLocaleTimeString() }}</span>
    </div>
  </div>
</template>
