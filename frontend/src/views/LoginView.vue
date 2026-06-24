<script setup lang="ts">
import { ref } from 'vue'
import axios from '@/lib/axios'
import { useRouter } from 'vue-router'
import { LogIn, Mail, Lock, ArrowRight, AlertCircle, Globe } from 'lucide-vue-next'
import type { AxiosError } from 'axios'

const router = useRouter()
const email = ref('')
const password = ref('')
const processing = ref(false)
const errorMsg = ref('')

const connexion = async () => {
  errorMsg.value = ''
  processing.value = true

  try {
    await axios.get('/sanctum/csrf-cookie')

    await axios.post('/login', {
      email: email.value,
      password: password.value
    })

    const response = await axios.get('/api/user')
    const user = response.data

    if (user.role === 'admin') {
      router.push('/admin')
    } else {
      router.push('/dashbord')
    }

  } catch (err: unknown) {
    const axiosError = err as AxiosError<{ message?: string }>
    errorMsg.value = axiosError.response?.data?.message || "Identifiants incorrects ou problème de connexion."
    console.error(axiosError)
  } finally {
    processing.value = false
  }
}


const loginWithGoogle = () => {
  window.location.href = `${import.meta.env.VITE_API_URL}/auth/google`
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-area">
        <div class="icon-wrapper">
          <LogIn class="w-10 h-10 text-indigo-600" />
        </div>
      </div>

      <h1>CONNEXION</h1>
      <p class="subtitle">Accédez à votre dashboard personnalisé</p>

      <form @submit.prevent="connexion">
        <div class="input-group">
          <Mail class="w-5 h-5 text-slate-400" />
          <input v-model="email" type="email" placeholder="Adresse Email" required />
        </div>

        <div class="input-group">
          <Lock class="w-5 h-5 text-slate-400" />
          <input v-model="password" type="password" placeholder="Mot de passe" required />
        </div>

        <div v-if="errorMsg" class="error-banner">
          <AlertCircle class="w-4 h-4" />
          <span>{{ errorMsg }}</span>
        </div>

        <button type="submit" :disabled="processing" class="btn-primary">
          <span>{{ processing ? 'Connexion...' : 'Se connecter' }}</span>
          <ArrowRight v-if="!processing" class="w-5 h-5" />
        </button>

        <div class="separator">
          <span>OU</span>
        </div>

        <button type="button" @click="loginWithGoogle" class="btn-oauth">
          <globe class="w-5 h-5" />
          <span>Continuer avec Google</span>
        </button>
      </form>

      <p class="login-link">
        Pas encore de compte ? <router-link to="/register">S'inscrire</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  background-color: #f8fafc;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  font-family: 'Inter', sans-serif;
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 100%;
  max-width: 400px;
  border: 1px solid #e2e8f0;
}

.icon-wrapper {
  background: #eef2ff;
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
  border: 1px solid transparent;
}

.input-group:focus-within {
  background-color: white;
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.input-group input {
  background: transparent;
  border: none;
  outline: none;
  width: 100%;
  margin-left: 12px;
  font-size: 15px;
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

.btn-primary {
  background-color: #4f46e5;
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
  margin-bottom: 16px;
}

.separator {
  display: flex;
  align-items: center;
  text-align: center;
  color: #94a3b8;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 16px;
}

.separator::before, .separator::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #e2e8f0;
}

.separator span {
  padding: 0 10px;
}

.btn-oauth {
  background-color: white;
  color: #1e293b;
  border: 1px solid #e2e8f0;
  padding: 12px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: background 0.2s;
}

.btn-oauth:hover {
  background-color: #f8fafc;
}

.login-link {
  margin-top: 24px;
  font-size: 14px;
  color: #64748b;
}

.login-link a {
  color: #4f46e5;
  text-decoration: none;
  font-weight: 700;
}
</style>
