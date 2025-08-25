<template v-if="hasPermission('view_post')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-2xl font-bold">📄 Danh sách bài viết món ăn</h2>
              <button class="btn btn-success-custom" @click="addPost" v-if="hasPermission('create_post')">
                <i class="bi bi-plus-circle me-1"></i> Thêm bài viết
              </button>
            </div>

            <!-- Tìm kiếm & Giới hạn hiển thị -->
            <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between align-items-center mb-3 gap-3">
              <!-- Tìm kiếm -->
              <div>
                <input type="text" v-model="search" placeholder="🔍 Tìm theo tên món hoặc nội dung..."
                  class="form-input-customer" />
              </div>

              <!-- Giới hạn hiển thị -->
              <div class="d-flex align-items-center gap-2 text-sm">
                Hiển thị
                <select v-model.number="perPage" class="form-select-customer">
                  <option :value="5">5</option>
                  <option :value="10">10</option>
                  <option :value="15">15</option>
                  <option :value="20">20</option>
                </select>
                mục/trang
              </div>
            </div>



            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="table table-bordered table-hover text-sm align-middle shadow rounded">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                    <th>Thể loại</th>
                    <th>Người viết</th>
                    <th>Ngày phát hành</th>
                    <th class="text-center">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="post in paginatedPosts" :key="post.id">
                    <td class="text-center">{{ post.id }}</td>
                    <td class="text-center">
                      <img :src="`http://127.0.0.1:8000/storage/img/post/${post.image}`" alt="Ảnh"
                        class="rounded border" style="width: 100px; height: 100px; object-fit: cover;" />
                    </td>
                    <td>{{ post.title }}</td>
                    <td>{{ truncate(post.content, 100) }}</td>
                    <td class="text-center">{{ post.category }}</td>
                    <td class="text-center">{{ post.user?.username || 'Ẩn danh' }}</td>
                    <td class="text-center">{{ formatDate(post.published_at) }}</td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-primary me-2" v-if="hasPermission('edit_post')"
                        @click="editPost(post)">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button v-if="hasPermission('hidden_post')" class="btn btn-sm btn-danger-custom me-2"
                        @click="toggleHide(post)">
                        <i class="fa-regular" :class="post.is_hidden ? 'fa-eye' : 'fa-eye-slash'"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredPosts.length === 0">
                    <td colspan="8" class="text-center text-muted py-4">Không tìm thấy kết quả.</td>
                  </tr>
                </tbody>

              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-between align-items-center text-sm">
              <span>Trang {{ currentPage }} / {{ totalPages }}</span>
              <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm" :disabled="currentPage === 1" @click="currentPage--">
                  ← Trước
                </button>
                <button class="btn btn-outline-secondary btn-sm" :disabled="currentPage === totalPages"
                  @click="currentPage++">
                  Sau →
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { onMounted } from 'vue'
import Swal from 'sweetalert2'
import router from '@/router'
import { Permission } from '@/stores/permission'
const API_URL = "http://127.0.0.1:8000/api"
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission } = Permission(userId)


// Props hoặc dữ liệu giả lập
const posts = ref([]) // Bạn có thể gán dữ liệu từ API sau
const search = ref('')
const perPage = ref(5)
const currentPage = ref(1)

const getAllPost = async () => {
  try {
    const res = await axios.get(`${API_URL}/get_all_post`);
    posts.value = res.data.result
    console.log(posts.value)
  } catch (error) {
    console.log(error);

  }
}


const addPost = () => {
  router.push('/admin/add_post')
}



// Computed: lọc bài viết theo từ khóa
const filteredPosts = computed(() => {
  const keyword = search.value.toLowerCase()
  return posts.value.filter((post) => {
    const title = post.title || ''
    const content = post.content || ''
    return (
      title.toLowerCase().includes(keyword) ||
      content.toLowerCase().includes(keyword)
    )
  })
})

const toggleHide = async (post) => {
  try {
    const res = await axios.post(`${API_URL}/post/${post.id}/toggle-hide`)

    post.is_hidden = res.data.post.is_hidden

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: res.data.message,
      showConfirmButton: false,
      timer: 2000
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Không thể thay đổi trạng thái bài viết'
    })
  }
}


// Tổng số trang
const totalPages = computed(() => {
  return Math.ceil(filteredPosts.value.length / perPage.value) || 1
})

// Phân trang
const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredPosts.value.slice(start, start + perPage.value)
})

// Watch search và perPage để reset currentPage
watch([search, perPage], () => {
  currentPage.value = 1
})

// Hàm rút gọn nội dung
const truncate = (text, length) => {
  return text.length > length ? text.substring(0, length) + '...' : text
}

// Format ngày tháng
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}

const editPost = (post) => {
  router.push(`/admin/edit_post/${post.id}`)
}


onMounted(() => {
  getAllPost()
})
</script>


<style scoped>
.input-group input {
  min-width: 240px;
}

.form-input-customer {
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 14px;
  width: 100%;
  max-width: 250px;
  transition: all 0.2s ease;
}

.form-input-customer:focus,
.form-select-customer:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
  border-color: #3b82f6;
}

.form-select-customer {
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px 10px;
  font-size: 14px;
  background-color: #fff;
}

.btn-danger-custom {
  background-color: gray;
  color: #fff;
}

.btn-success-custom {
  background-color: #c92c3c;
  color: #fff;
}
</style>
