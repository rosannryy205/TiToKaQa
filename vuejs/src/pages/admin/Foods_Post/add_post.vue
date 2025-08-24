<template>
  <div class="container py-5">
    <div class="card shadow">
      <div class="card-body">
        <h2 class="card-title mb-4">📝 Thêm bài viết mới</h2>

        <!-- Tiêu đề -->
        <div class="mb-3">
          <label for="title" class="form-label fw-semibold">Tiêu đề bài viết</label>
          <input type="text" id="title" class="form-control" placeholder="Nhập tiêu đề..." v-model="title" />
        </div>

        <!-- Ảnh đại diện -->
        <div class="mb-3">
          <label for="image" class="form-label fw-semibold">Ảnh đại diện</label>
          <input type="file" id="image" class="form-control" @change="handleImageUpload" />
          <div v-if="imagePreview" class="mt-3">
            <img :src="imagePreview" class="img-thumbnail" style="max-width: 300px;" />
          </div>
        </div>

        <!-- Chọn danh mục -->
        <div class="mb-3">
          <label for="category" class="form-label fw-semibold">Chọn danh mục</label>
          <select id="category" class="form-select" v-model="selectedCategory">
            <option disabled value="">-- Chọn danh mục --</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
        </div>


        <!-- Nội dung -->
        <div class="mb-4">
          <label for="content" class="form-label fw-semibold">Nội dung bài viết</label>
          <textarea id="content" v-model="content" class="form-control no-radius" rows="7"
            placeholder="Nhập nội dung bài viết..."></textarea>
        </div>


        <!-- Hành động -->
        <div class="d-flex gap-2">
          <button class="btn btn-new" @click="savePost">💾 Lưu bài viết</button>
          <button class="btn btn-outline-secondary" @click="generateWithAI">🤖 Dùng AI viết</button>
          <button class="btn btn-checkseo" @click="checkSEO">🔍 Kiểm tra SEO</button>
          <button class="btn btn-goback" @click="goBack()">Quay lại</button>
        </div>

        <div v-if="seoResult">
          <h5>📊 Điểm SEO: {{ seoResult.score }}/100</h5>
          <p><strong>✅ Ưu điểm:</strong></p>
          <ul>
            <li v-for="(item, idx) in seoResult.strengths" :key="idx">{{ item }}</li>
          </ul>

          <p><strong>⚠️ Nhược điểm:</strong></p>
          <ul>
            <li v-for="(item, idx) in seoResult.weaknesses" :key="idx">{{ item }}</li>
          </ul>

          <p><strong>💡 Gợi ý cải thiện:</strong></p>
          <ul>
            <li v-for="(item, idx) in seoResult.recommendations" :key="idx">{{ item }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const API_URL = "http://127.0.0.1:8000/api"
const title = ref('')
const content = ref('')
const selectedCategory = ref('') // Danh mục được chọn
const imageFile = ref(null)
const imagePreview = ref('')
const categories = ref(['Ẩm thực', 'Khuyến mãi', 'Tin tức']) // Danh mục cố định
const seoResult = ref(null)
const goBack = () => window.history.back()

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const savePost = async () => {
  try {
    const formData = new FormData()
    formData.append("title", title.value)
    formData.append("content", content.value)
    formData.append("category", selectedCategory.value)

    if (imageFile.value) {
      formData.append("image", imageFile.value)
    }

    // ✅ Lấy user object từ localStorage
    const user = JSON.parse(localStorage.getItem("user"))
    if (!user || !user.id) {
      Swal.fire({
        icon: "error",
        title: "Không tìm thấy thông tin user",
        text: "Vui lòng đăng nhập lại!",
      })
      return
    }

    // ✅ Lấy id gắn vào formData
    formData.append("user_id", user.id)

    await axios.post(`${API_URL}/insert_post`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })

    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Thêm bài viết thành công",
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
    })

    goBack()
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: "error",
      title: "Lỗi khi thêm bài viết",
    })
  }
}


const generateWithAI = async () => {
  if (!title.value) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Vui lòng nhập tiêu đề trước khi dùng AI!',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    })
    return
  }

  try {
    const res = await axios.post(`${API_URL}/generate/post`, {
      title: title.value
    })

    const fullText = res.data.content || ""
    content.value = ""

    let index = 0
    const speed = 20

    const typing = setInterval(() => {
      if (index < fullText.length) {
        content.value += fullText[index]
        index++
      } else {
        clearInterval(typing)
      }
    }, speed)

  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Đã xảy ra lỗi khi gọi AI!',
      confirmButtonColor: '#c92c3c'
    })
  }
}

const checkSEO = async () => {
  if (!title.value || !content.value) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'warning',
      title: 'Nhập đầy đủ tiêu đề và nội dung trước khi kiểm tra SEO',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true
    })
    return
  }

  try {
    const res = await axios.post(`${API_URL}/check-seo`, {
      title: title.value,
      content: content.value
    })
    seoResult.value = res.data
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Đã xảy ra lỗi khi kiểm tra SEO',
      confirmButtonColor: '#c92c3c'
    })
  }
}
</script>



<style>
.btn-new {
  background-color: #c92c3c;
  color: #fff;
}

.btn-new:hover {
  border: 1px solid #c92c3c;
  color: #c92c3c;
}

.btn-checkseo {
  background-color: rgb(46, 46, 175);
  color: #fff;
}

.btn-checkseo:hover {
  border: 1px solid rgb(46, 46, 175);
  color: rgb(46, 46, 175);
}

.typing-box {
  border: 1px solid #ddd;
  padding: 10px;
  min-height: 150px;
  white-space: pre-wrap;
}

.cursor {
  display: inline-block;
  width: 1px;
  background-color: black;
  animation: blink 0.8s infinite;
}

.no-radius {
  border-radius: 0 !important;
}

.btn-goback {
  background-color: gray;
  color: #fff;
}

.btn-goback:hover {
  border: 1px solid gray;
  color: gray;
}

@keyframes blink {

  0%,
  50% {
    opacity: 1;
  }

  50.1%,
  100% {
    opacity: 0;
  }
}
</style>
