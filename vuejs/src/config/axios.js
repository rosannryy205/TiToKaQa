import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL, 
  withCredentials: true, 
})

api.defaults.xsrfCookieName = 'XSRF-TOKEN'
api.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
export default api
