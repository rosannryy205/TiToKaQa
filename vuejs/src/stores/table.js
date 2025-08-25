import axios from 'axios';
import { ref, onMounted } from 'vue';
import { API_URL } from '@/config'
export const Table = {
  setup(){
    const tables = ref([])

    const getTable = async () => {
      try {
        const res = await axios.get(`${API_URL}/tables`)
        tables.value = res.data
        // console.log(tables.value);
      } catch (error) {
        console.log(error);
      }
    }


    onMounted(() => {
      getTable();
    })

    return{
      tables,
    }
  }
}
