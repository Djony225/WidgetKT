import { defineStore } from 'pinia'
import axios from '@/lib/axios'


interface User {
  id: number;
  name: string;
  email: string;
  birthday?: string;
  email_verified_at?: string | null;
  created_at?: string;
  updated_at?: string;
}


interface AuthState {
  user: User | null;
  isLoggedIn: boolean;
}

export const useAuthStore = defineStore('auth', {

  state: (): AuthState => ({
    user: null,
    isLoggedIn: false
  }),

  actions: {

    async fetchUser(): Promise<void> {
      try {
        const response = await axios.get<User>('/api/user')
        this.user = response.data
        this.isLoggedIn = true
      } catch (error) {
         console.error("Erreur :", error)
        this.user = null
        this.isLoggedIn = false
      }
    },

    async logout(): Promise<void> {
      try {
        await axios.post('/logout')

        this.user = null
        this.isLoggedIn = false

        delete axios.defaults.headers.common['X-XSRF-TOKEN']

        window.location.replace('/')
      } catch (error) {
        console.error("Échec de la déconnexion :", error)
        window.location.replace('/')
      }
    }
  }
})
