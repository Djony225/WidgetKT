<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { LayoutDashboard, LogIn, UserPlus } from 'lucide-vue-next'
</script>

<template>
  <div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden bg-slate-950">

    <div
      class="absolute top-0 left-0 w-full h-full bg-cover bg-center z-0 scale-105"
      style="background-image: url('https://static.wixstatic.com/media/51cfd3_b3aa14c5f9634f76be70b9f206e6a1bf~mv2.png/v1/fill/w_430,h_365,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/51cfd3_b3aa14c5f9634f76be70b9f206e6a1bf~mv2.png');"
    ></div>

    <div class="absolute top-0 left-0 w-full h-full bg-slate-950/75 backdrop-blur-md z-10"></div>

    <div class="relative z-20 w-full max-w-2xl flex flex-col items-center">

      <div class="text-center mb-12">
        <div class="bg-indigo-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/30">
          <LayoutDashboard class="text-white w-12 h-12" />
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-3 tracking-tight">Widget KT Service</h1>
        <p class="text-slate-300 max-w-md mx-auto text-base">
          Personnalisez votre espace de travail en regroupant tous vos services favoris au même endroit.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
        <RouterLink
          to="/login"
          class="flex flex-col items-start p-8 bg-white/90 border border-white/20 rounded-3xl hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/20 hover:-translate-y-1 transition-all duration-300 backdrop-blur-md"
        >
          <div class="bg-indigo-100 p-3 rounded-xl mb-4 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
            <LogIn class="w-6 h-6" />
          </div>
          <h2 class="text-xl font-bold text-slate-900 mb-1">Se connecter</h2>
          <p class="text-slate-500 text-sm italic">Accédez à vos widgets personnalisés.</p>
        </RouterLink>

        <RouterLink
          to="/register"
          class="flex flex-col items-start p-8 bg-white/90 border border-white/20 rounded-3xl hover:border-emerald-500 hover:shadow-2xl hover:shadow-emerald-500/20 hover:-translate-y-1 transition-all duration-300 backdrop-blur-md"
        >
          <div class="bg-emerald-100 p-3 rounded-xl mb-4 text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
            <UserPlus class="w-6 h-6" />
          </div>
          <h2 class="text-xl font-bold text-slate-900 mb-1">Créer un compte</h2>
          <p class="text-slate-500 text-sm italic">Commencez à configurer votre dashboard.</p>
        </RouterLink>
      </div>

      <footer class="mt-16 text-slate-500 text-sm tracking-wide">
        <p>Projet C2 - Micro Service</p>
      </footer>
    </div>
  </div>
</template>

