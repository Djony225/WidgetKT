import { defineStore } from 'pinia'
import axios from '@/lib/axios'


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

interface DashboardState {
  userWidgets: DashboardWidget[];
  availableWidgets: Widget[];
  loading: boolean;
}

export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    userWidgets: [],
    availableWidgets: [],
    loading: false
  }),

  actions: {
    async fetchUserWidgets(): Promise<void> {
      this.loading = true
      try {
        const response = await axios.get<DashboardWidget[]>('/api/dashboard')
        this.userWidgets = response.data
      } catch (error) {
        console.error("Erreur lors de la récupération des widgets du dashboard:", error)
      } finally {
        this.loading = false
      }
    },

    async fetchAvailableWidgets(): Promise<void> {
      try {
        const response = await axios.get<Widget[]>('/api/widgets')
        this.availableWidgets = response.data
      } catch (error) {
        console.error("Erreur lors de la récupération du catalogue de widgets:", error)
      }
    },


    async addWidgetToDashboard(widgetId: number, params: Record<string, string | number> = {}): Promise<void> {
      try {

        const response = await axios.post<DashboardWidget>('/api/dashboard', {
          widget_id: widgetId,
          refresh_rate: 60,
          position: this.userWidgets.length,
          params: params
        })

        this.userWidgets.push(response.data)
      } catch (error) {
        console.error("Erreur lors de l'ajout du widget:", error)
      }
    },


    async removeWidget(dashboardWidgetId: number): Promise<void> {
      try {
        await axios.delete(`/api/dashboard/${dashboardWidgetId}`)
        this.userWidgets = this.userWidgets.filter(w => w.id !== dashboardWidgetId)
      } catch (error) {
        console.error("Erreur lors de la suppression du widget:", error)
      }
    }
  }
})
