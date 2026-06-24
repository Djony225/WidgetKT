import { defineStore } from 'pinia'
import axios from '@/lib/axios'

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

interface Service {
  id: number;
  name: string;
  description: string | null;
}

interface Widget {
  id: number;
  name: string;
  description: string | null;
  service_id: number;
}

interface AdminState {
  users: User[];
  services: Service[];
  widgets: Widget[];
  loading: boolean;
}

export const useAdminStore = defineStore('admin', {
  state: (): AdminState => ({
    users: [],
    services: [],
    widgets: [],
    loading: false
  }),

  getters: {
    totalUsers(): number { return this.users.length },
    totalServices(): number { return this.services.length },
    totalWidgets(): number { return this.widgets.length }
  },

  actions: {
    async fetchAdminData(): Promise<void> {
      this.loading = true
      try {
        const [usersRes, servicesRes, widgetsRes] = await Promise.all([
          axios.get<User[]>('/api/admin/users'),
          axios.get<Service[]>('/api/services'),
          axios.get<Widget[]>('/api/widgets')
        ])
        this.users = usersRes.data
        this.services = servicesRes.data
        this.widgets = widgetsRes.data
      } catch (error) {
        console.error("Erreur lors du chargement des données admin :", error)
      } finally {
        this.loading = false
      }
    },

    async updateUser(id: number, payload: { role: string }): Promise<void> {
      try {
        const response = await axios.put<User>(`/api/admin/users/${id}`, payload)
        const index = this.users.findIndex(u => u.id === id)
        if (index !== -1) {
          this.users[index] = response.data
        }
      } catch (error) {
        console.error("Erreur lors de la modification de l'utilisateur :", error)
      }
    },


    async destroyUser(id: number): Promise<void> {
      try {
        await axios.delete(`/api/admin/users/${id}`)
        this.users = this.users.filter(u => u.id !== id)
      } catch (error) {
        console.error("Erreur lors de la suppression de l'utilisateur :", error)
      }
    }


  }
})
