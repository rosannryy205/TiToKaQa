import axios from 'axios';
import { ref, onMounted } from 'vue';
export const Table = {
  setup(){
    const tables = ref([])

    const getTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/tables')
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
