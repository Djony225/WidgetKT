<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { CalendarDays, RefreshCw, MapPin, Trash2, Sun, Cloud, CloudRain, CloudLightning, HelpCircle } from 'lucide-vue-next'
import axios from 'axios'
import { useDashboardStore } from '@/stores/dashboardWidgets'

interface DailyForecast {
  date: string;
  temp: number;
  description: string;
  mainCondition: string;
}

const props = defineProps<{
  widgetId: number
  initialCity: string
  refreshInterval: number
}>()

const dashboardStore = useDashboardStore()

const forecasts = ref<DailyForecast[]>([])
const cityName = ref('')
const loading = ref(true)
const errorMessage = ref<string | null>(null)
const lastUpdate = ref(new Date())

let timer: ReturnType<typeof setInterval> | null = null
const API_KEY = "44132efecb4498db65d12b831ba098c0"

const getWeatherIcon = (condition: string) => {
  switch (condition.toLowerCase()) {
    case 'clear': return Sun
    case 'clouds': return Cloud
    case 'rain':
    case 'drizzle': return CloudRain
    case 'thunderstorm': return CloudLightning
    default: return HelpCircle
  }
}

const fetchForecast = async () => {
  loading.value = true
  errorMessage.value = null
  try {

    const response = await axios.get(
      `https://api.openweathermap.org/data/2.5/forecast?q=${props.initialCity}&units=metric&lang=fr&appid=${API_KEY}`
    )

    cityName.value = response.data.city.name
    const list = response.data.list

    const dailyData: DailyForecast[] = []
    const datesTraitees = new Set<string>()

    for (const item of list) {
      const dateObj = new Date(item.dt * 1000)
      const dateString = dateObj.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' })
      const heure = dateObj.getHours()

      if (!datesTraitees.has(dateString) && (heure === 12 || heure === 13 || heure === 11 || dailyData.length === 0)) {
        dailyData.push({
          date: dateString,
          temp: Math.round(item.main.temp),
          description: item.weather[0].description,
          mainCondition: item.weather[0].main
        })
        datesTraitees.add(dateString)
      }

      if (dailyData.length === 4) break
    }

    forecasts.value = dailyData
    lastUpdate.value = new Date()
  } catch (error) {
    console.error("Erreur prévisions météo:", error)

  } finally {
    loading.value = false
  }
}

watch(() => props.initialCity, () => {
  fetchForecast()
})

const deleteWidget = async (): Promise<void> => {
  if (confirm(`Voulez-vous vraiment supprimer le widget météo de ${props.initialCity} ?`)) {
    await dashboardStore.removeWidget(props.widgetId)
  }
}

onMounted(() => {
  fetchForecast()
  const intervalMs = Math.max(props.refreshInterval, 30) * 1000
  timer = setInterval(fetchForecast, intervalMs)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group">
    <div class="flex justify-between items-start mb-4">
      <div class="bg-blue-100 p-3 rounded-2xl text-blue-600">
        <CalendarDays class="w-6 h-6" />
      </div>
      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button @click="fetchForecast" :disabled="loading" class="p-2 text-slate-400 hover:text-blue-600 disabled:opacity-50">
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>
        <button @click="deleteWidget" class="p-2 text-slate-400 hover:text-red-600">
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </div>

    <div v-if="loading && forecasts.length === 0" class="animate-pulse space-y-3">
      <div class="h-6 bg-slate-100 rounded w-1/3"></div>
      <div class="grid grid-cols-4 gap-2 pt-2">
        <div v-for="i in 4" :key="i" class="h-16 bg-slate-100 rounded-xl"></div>
      </div>
    </div>

    <div v-else-if="errorMessage" class="text-sm text-red-500 py-4">
      <p class="font-medium">Erreur Prévisions :</p>
      <p class="text-xs text-slate-500">{{ errorMessage }}</p>
    </div>

    <div v-else-if="forecasts.length > 0">
      <div class="flex items-center gap-2 text-slate-500 mb-3">
        <MapPin class="w-4 h-4" />
        <span class="text-sm font-medium uppercase tracking-wider">{{ cityName }} (4 Jours)</span>
      </div>

      <div class="grid grid-cols-4 gap-2 bg-slate-50 p-3 rounded-2xl">
        <div v-for="(day, index) in forecasts" :key="index" class="flex flex-col items-center justify-center text-center p-1">
          <span class="text-[10px] font-semibold text-slate-400 capitalize mb-1">{{ day.date }}</span>
          <component :is="getWeatherIcon(day.mainCondition)" class="w-5 h-5 text-slate-600 mb-1" />
          <span class="text-sm font-bold text-slate-800">{{ day.temp }}°C</span>
        </div>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-50 text-[10px] text-slate-400 flex justify-between">
      <span>Mise à jour à {{ lastUpdate.toLocaleTimeString() }}</span>
    </div>
  </div>
</template>
