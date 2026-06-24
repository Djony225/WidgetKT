<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { MessageSquare, RefreshCw, Trash2, ArrowUp, ExternalLink } from 'lucide-vue-next'
import axios from 'axios'
import { useDashboardStore } from '@/stores/dashboardWidgets'

interface RedditPostData {
  id: string;
  title: string;
  author: string;
  permalink: string;
  ups: number;
  num_comments: number;
  created_utc: number;
}

interface RedditPostChildren {
  kind: string;
  data: RedditPostData;
}

interface RedditResponse {
  data: {
    children: RedditPostChildren[];
  };
}

const props = defineProps<{
  widgetId: number
  subreddit: string
  limit: number
}>()

const dashboardStore = useDashboardStore()

const posts = ref<RedditPostData[]>([])
const loading = ref<boolean>(true)
const errorMessage = ref<string | null>(null)
const lastUpdate = ref<Date>(new Date())

let timer: ReturnType<typeof setInterval> | null = null

const fetchRedditPosts = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = null

  const cleanSubreddit = props.subreddit.trim().replace(/^r\//, '')

  if (!cleanSubreddit) {
    errorMessage.value = "Nom du subreddit vide"
    loading.value = false
    return
  }

  try {
    const response = await axios.get<RedditResponse>(
      `https://www.reddit.com/r/${cleanSubreddit}/new.json?limit=${props.limit}`
    )

    posts.value = response.data.data.children.map((child) => child.data)
    lastUpdate.value = new Date()
  } catch (error: unknown) {
    console.error("Erreur Reddit:", error)

    if (axios.isAxiosError(error) && error.response?.status === 404) {
      errorMessage.value = `Subreddit "r/${cleanSubreddit}" introuvable`
    } else {
      errorMessage.value = "Impossible de charger le flux Reddit"
    }
  } finally {
    loading.value = false
  }
}

const deleteWidget = async (): Promise<void> => {
  if (confirm(`Voulez-vous supprimer le widget Reddit de r/${props.subreddit} ?`)) {
    await dashboardStore.removeWidget(props.widgetId)
  }
}

watch(() => [props.subreddit, props.limit], () => {
  fetchRedditPosts()
})

onMounted(() => {
  fetchRedditPosts()
  timer = setInterval(fetchRedditPosts, 300000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative group flex flex-col justify-between min-h-[350px]">
    <div>
      <div class="flex justify-between items-start mb-4">
        <div class="bg-orange-50 p-3 rounded-2xl text-orange-600 flex items-center gap-2">
          <MessageSquare class="w-6 h-6" />
          <span class="text-xs font-bold uppercase tracking-wider bg-orange-100 px-2 py-0.5 rounded-full">Reddit</span>
        </div>
        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
          <button @click="fetchRedditPosts" :disabled="loading" class="p-2 text-slate-400 hover:text-orange-600 transition-colors disabled:opacity-50">
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
          </button>
          <button @click="deleteWidget" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </div>

      <div class="text-sm font-bold text-slate-400 mb-4 tracking-wide uppercase">
        r/{{ subreddit.replace(/^r\//, '') }}
      </div>

      <div v-if="loading && posts.length === 0" class="space-y-3 animate-pulse">
        <div v-for="i in 3" :key="i" class="space-y-1 py-2 border-b border-slate-50">
          <div class="h-4 bg-slate-100 rounded w-5/6"></div>
          <div class="h-3 bg-slate-100 rounded w-1/3"></div>
        </div>
      </div>

      <div v-else-if="errorMessage" class="text-sm text-red-500 py-6 text-center">
        <p class="font-semibold">{{ errorMessage }}</p>
      </div>

      <div v-else-if="posts.length > 0" class="space-y-3 max-h-72 overflow-y-auto pr-1">
        <div v-for="post in posts" :key="post.id" class="p-3 bg-slate-50 hover:bg-slate-100/80 rounded-2xl transition-colors relative group/item">
          <a
            :href="`https://www.reddit.com${post.permalink}`"
            target="_blank"
            class="block pr-5 text-sm font-semibold text-slate-800 hover:text-orange-600 transition-colors line-clamp-2"
          >
            {{ post.title }}
            <ExternalLink class="w-3 h-3 inline ml-1 opacity-0 group-hover/item:opacity-100 transition-opacity text-slate-400" />
          </a>

          <div class="flex items-center gap-3 mt-2 text-[11px] text-slate-400 font-medium">
            <span class="text-slate-500">par u/{{ post.author }}</span>
            <span class="flex items-center gap-0.5 text-orange-600">
              <ArrowUp class="w-3 h-3" /> {{ post.ups }}
            </span>
            <span class="flex items-center gap-0.5 text-blue-500">
              <MessageSquare class="w-3 h-3" /> {{ post.num_comments }}
            </span>
          </div>
        </div>
      </div>

      <div v-else class="text-center text-slate-400 text-sm py-6">
        Aucun post récent disponible.
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-50 text-[10px] text-slate-400 flex justify-between">
      <span>Max: {{ limit }} posts</span>
      <span>Mis à jour à {{ lastUpdate.toLocaleTimeString() }}</span>
    </div>
  </div>
</template>
