import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue({
    template: {
      compilerOptions: {
        isCustomElement: (tag) => tag === 'df-messenger'
      }
    }
  }),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    host: true, // 👈 Quan trọng: Cho phép truy cập từ IP nội bộ
    port: 5173, // 👈 Không bắt buộc, nhưng nên khai báo rõ
  },

})
