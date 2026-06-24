<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Youtube, RefreshCw, Trash2, Eye, ThumbsUp, MessageSquare, ExternalLink } from 'lucide-vue-next'
import axios from 'axios'
import { useDashboardStore } from '@/stores/dashboardWidgets'

interface YoutubeVideoSnippet {
  title: string;
  channelTitle: string;
  thumbnails: {
    medium: {
      url: string;
    };
  };
}

interface YoutubeVideoStatistics {
  viewCount: string;
  likeCount: string;
  commentCount: string;
}

interface YoutubeVideoItem {
  id: string;
  snippet: YoutubeVideoSnippet;
  statistics: YoutubeVideoStatistics;
}

interface YoutubeApiResponse {
  items?: YoutubeVideoItem[];
}

const props = defineProps<{
  widgetId: number
  videoId: string
  refreshInterval: number
}>()

const dashboardStore = useDashboardStore()

const videoData = ref<YoutubeVideoItem | null>(null)
const loading = ref<boolean>(true)
const errorMessage = ref<string | null>(null)
const lastUpdate = ref<Date>(new Date())

let timer: ReturnType<typeof setInterval> | null = null

const API_KEY = "AIzaSyBSVoBLws4RnqepvbT951TFeJbJc1fztC4"

const fetchYoutubeStats = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = null

  const cleanVideoId = props.videoId.trim()

  if (!cleanVideoId) {
    errorMessage.value = "ID de la vidéo manquant"
    loading.value = false
    return
  }

  try {
    const response = await axios.get<YoutubeApiResponse>(
      `https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&id=${cleanVideoId}&key=${API_KEY}`
    )

    if (response.data.items && response.data.items[0]) {
      videoData.value = response.data.items[0]
      lastUpdate.value = new Date()
    } else {
      errorMessage.value = "Vidéo introuvable"
    }
  } catch (error: unknown) {
    console.error("Erreur YouTube:", error)
    errorMessage.value = "Erreur lors de la récupération des statistiques"
  } finally {
    loading.value = false
  }
}

const deleteWidget = async (): Promise<void> => {
  if (confirm("Voulez-vous supprimer ce widget YouTube ?")) {
    await dashboardStore.removeWidget(props.widgetId)
  }
}

const formatNumber = (numStr: string): string => {
  const num = parseInt(numStr, 10)
  return isNaN(num) ? numStr : num.toLocaleString('fr-FR')
}

watch(() => [props.videoId, props.refreshInterval], () => {
  fetchYoutubeStats()
})

onMounted(() => {
  fetchYoutubeStats()
  timer = setInterval(fetchYoutubeStats, props.refreshInterval * 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group flex flex-col justify-between min-h-[350px]">
    <div>
      <div class="flex justify-between items-start mb-4">
        <div class="bg-red-50 p-3 rounded-2xl text-red-600 flex items-center gap-2">
          <Youtube class="w-6 h-6" />
          <span class="text-xs font-bold uppercase tracking-wider bg-red-100 px-2 py-0.5 rounded-full">YouTube</span>
        </div>
        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
          <button @click="fetchYoutubeStats" :disabled="loading" class="p-2 text-slate-400 hover:text-red-600 transition-colors disabled:opacity-50">
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
          </button>
          <button @click="deleteWidget" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </div>

      <div v-if="loading && !videoData" class="space-y-4 animate-pulse">
        <div class="h-40 bg-slate-100 rounded-2xl w-full"></div>
        <div class="h-4 bg-slate-100 rounded w-5/6"></div>
        <div class="h-3 bg-slate-100 rounded w-1/2"></div>
      </div>

      <div v-else-if="errorMessage" class="text-sm text-red-500 py-12 text-center">
        <p class="font-semibold">{{ errorMessage }}</p>
      </div>

      <div v-else-if="videoData" class="space-y-4">
        <div class="relative rounded-2xl overflow-hidden group/thumb aspect-video bg-slate-900">
          <img
            :src="videoData.snippet.thumbnails.medium.url"
            :alt="videoData.snippet.title"
            class="w-full h-full object-cover opacity-90 group-hover/thumb:scale-105 transition-transform duration-300"
          />
          <a
            :href="`https://www.youtube.com/watch?v=${videoData.id}`"
            target="_blank"
            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition-opacity text-white font-medium text-xs gap-1"
          >
            Voir la vidéo <ExternalLink class="w-3 h-3" />
          </a>
        </div>

        <div>
          <h4 class="text-sm font-bold text-slate-800 line-clamp-2 hover:text-red-600 transition-colors">
            {{ videoData.snippet.title }}
          </h4>
          <p class="text-[11px] font-semibold text-slate-400 mt-0.5">{{ videoData.snippet.channelTitle }}</p>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-50">
          <div class="bg-slate-50 p-2 rounded-xl text-center">
            <Eye class="w-4 h-4 text-slate-400 mx-auto mb-1" />
            <span class="text-xs font-bold text-slate-700 block">{{ formatNumber(videoData.statistics.viewCount) }}</span>
            <span class="text-[9px] text-slate-400 block font-medium">Vues</span>
          </div>
          <div class="bg-slate-50 p-2 rounded-xl text-center">
            <ThumbsUp class="w-4 h-4 text-slate-400 mx-auto mb-1" />
            <span class="text-xs font-bold text-slate-700 block">{{ formatNumber(videoData.statistics.likeCount) }}</span>
            <span class="text-[9px] text-slate-400 block font-medium">Likes</span>
          </div>
          <div class="bg-slate-50 p-2 rounded-xl text-center">
            <MessageSquare class="w-4 h-4 text-slate-400 mx-auto mb-1" />
            <span class="text-xs font-bold text-slate-700 block">{{ formatNumber(videoData.statistics.commentCount) }}</span>
            <span class="text-[9px] text-slate-400 block font-medium">Comms</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-50 text-[10px] text-slate-400 flex justify-between">
      <span>Màj : {{ refreshInterval }}s</span>
      <span>Mis à jour à {{ lastUpdate.toLocaleTimeString() }}</span>
    </div>
  </div>
</template>
