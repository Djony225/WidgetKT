<script setup lang="ts">
import { ref } from 'vue'
import axios from '@/lib/axios'
import { useRouter } from 'vue-router'
import { UserPlus, Mail, Lock, User, ArrowRight, AlertCircle } from 'lucide-vue-next'
import type { AxiosError } from 'axios'

const router = useRouter()


const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const processing = ref(false)
const errorMsg = ref('')

const inscription = async () => {
  errorMsg.value = ''
  processing.value = true

  try {

    if (password.value !== password_confirmation.value) {
      errorMsg.value = "Les mots de passe ne correspondent pas."
      return
    }
    delete axios.defaults.headers.common['X-XSRF-TOKEN'];

    await axios.get('/sanctum/csrf-cookie')

    const res = await axios.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    console.log("Réponse inscription :", res.status)


    router.push('/login')
  } catch (err: unknown) {
    const axiosError = err as AxiosError<{ message?: string }>
    errorMsg.value = axiosError.response?.data?.message || "Erreur lors de l'inscription."
    console.error(axiosError)
  }
  finally {
    processing.value = false
  }
}
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <div class="logo-area">
        <div class="icon-wrapper">
          <UserPlus class="w-10 h-10 text-blue-600" />
        </div>
      </div>

      <h1>INSCRIPTION</h1>
      <p class="subtitle">Créez votre compte pour accéder au Dashboard</p>

      <form @submit.prevent="inscription">
        <div class="input-group">
          <User class="w-5 h-5 text-slate-400" />
          <input v-model="name" type="text" placeholder="Nom d'utilisateur" required />
        </div>

        <div class="input-group">
          <Mail class="w-5 h-5 text-slate-400" />
          <input v-model="email" type="email" placeholder="Adresse Email" required />
        </div>

        <div class="input-group">
          <Lock class="w-5 h-5 text-slate-400" />
          <input v-model="password" type="password" placeholder="Mot de passe" required />
        </div>

        <div class="input-group">
          <Lock class="w-5 h-5 text-slate-400" />
          <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" required />
        </div>

        <div v-if="errorMsg" class="error-banner">
          <AlertCircle class="w-4 h-4" />
          <span>{{ errorMsg }}</span>
        </div>

        <button type="submit" :disabled="processing">
          <span>{{ processing ? 'Traitement...' : 'Créer mon compte' }}</span>
          <ArrowRight v-if="!processing" class="w-5 h-5" />
        </button>
      </form>

      <p class="login-link">
        Déjà inscrit ? <router-link to="/login">Connectez-vous ici</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  background-color: #f8fafc;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  font-family: 'Inter', sans-serif;
}

.register-card {
  background: white;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 100%;
  max-width: 440px;
  border: 1px solid #e2e8f0;
}

.icon-wrapper {
  background: #eff6ff;
  width: 70px;
  height: 70px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

h1 {
  font-size: 24px;
  font-weight: 800;
  color: #1e293b;
  letter-spacing: -0.025em;
  margin-bottom: 8px;
}

.subtitle {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 32px;
}

.input-group {
  background-color: #f1f5f9;
  border-radius: 12px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  padding: 12px 16px;
  transition: all 0.2s;
  border: 1px solid transparent;
}

.input-group:focus-within {
  background-color: white;
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.input-group input {
  background: transparent;
  border: none;
  outline: none;
  width: 100%;
  margin-left: 12px;
  font-size: 15px;
  color: #1e293b;
}

.error-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #fef2f2;
  color: #b91c1c;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 16px;
}

button {
  background-color: #1e293b;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: transform 0.1s, background 0.2s;
}

button:hover:not(:disabled) {
  background-color: #0f172a;
  transform: translateY(-1px);
}

button:active {
  transform: translateY(0);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-link {
  margin-top: 24px;
  font-size: 14px;
  color: #64748b;
}

.login-link a {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 700;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>
