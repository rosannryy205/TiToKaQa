<template>
  <div>
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">📄 Danh sách bài viết món ăn</h2>
        <button class="btn btn-success" @click="addPost">
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
              <th>Tên món</th>
              <th>Nội dung</th>
              <th>Ngày phát hành</th>
              <th class="text-center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in paginatedPosts" :key="post.id">
              <td class="text-center">{{ post.id }}</td>
              <td class="text-center">
                <img :src="post.image" alt="Ảnh" class="rounded border"
                  style="width: 80px; height: 56px; object-fit: cover;" />
              </td>
              <td>{{ post.food_name }}</td>
              <td>{{ truncate(post.content, 100) }}</td>
              <td class="text-center">{{ formatDate(post.published_at) }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-primary me-2" @click="editPost(post)">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <!-- <button class="btn btn-sm btn-danger" @click="deletePost(post)">
                <i class="bi bi-trash"></i>
              </button> -->
              </td>
            </tr>
            <tr v-if="filteredPosts.length === 0">
              <td colspan="6" class="text-center text-muted py-4">Không tìm thấy kết quả.</td>
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
</template>

<script>
export default {
  name: 'FoodPostTable',
  data() {
    return {
      search: '',
      currentPage: 1,
      perPage: 5,
      posts: [
        {
          id: 1,
          food_name: 'Mì Kim Chi Thập Cẩm',
          image: '/uploads/food_posts/food_1.jpg',
          content: 'Mì Kim Chi Thập Cẩm là món ăn được yêu thích bởi sự kết hợp hoàn hảo giữa bò Mỹ, hải sản tươi sống và kim chi đậm đà...',
          published_at: '2025-07-20',
        },
        {
          id: 2,
          food_name: 'Mì Kim Chi Đùi Gà',
          image: '/uploads/food_posts/food_2.jpg',
          content: 'Mì Kim Chi Đùi Gà với phần đùi gà mềm ngọt và nước dùng cay nhẹ tạo nên một hương vị đậm đà khó quên...',
          published_at: '2025-07-18',
        },
        {
          id: 3,
          food_name: 'Mì Kim Chi Hải Sản',
          image: '/uploads/food_posts/food_3.jpg',
          content: 'Món ăn dành cho tín đồ hải sản, với tôm mực tươi ngon và vị chua cay đặc trưng của kim chi...',
          published_at: '2025-07-15',
        },
        {
          id: 4,
          food_name: 'Mì Kim Chi Bò Mỹ',
          image: '/uploads/food_posts/food_4.jpg',
          content: 'Thịt bò Mỹ mềm tan kết hợp với sợi mì và nước dùng kim chi đậm đà, đây là món ăn đặc biệt yêu thích...',
          published_at: '2025-07-12',
        },
        {
          id: 5,
          food_name: 'Mì Kim Chi Cá',
          image: '/uploads/food_posts/food_5.jpg',
          content: 'Mì Kim Chi Cá là sự hòa quyện giữa vị chua cay và độ ngọt tự nhiên của thịt cá, ăn hoài không ngán...',
          published_at: '2025-07-10',
        },
        {
          id: 6,
          food_name: 'Mì Cay Hàn Quốc',
          image: '/uploads/food_posts/food_6.jpg',
          content: 'Mì cay truyền thống với công thức đậm chất Hàn Quốc, dành cho ai mê vị cay nồng và đậm đà...',
          published_at: '2025-07-08',
        },
      ],
    }
  },
  computed: {
    filteredPosts() {
      const keyword = this.search.toLowerCase()
      return this.posts.filter(
        (post) =>
          post.food_name.toLowerCase().includes(keyword) ||
          post.content.toLowerCase().includes(keyword)
      )
    },
    totalPages() {
      return Math.ceil(this.filteredPosts.length / this.perPage) || 1
    },
    paginatedPosts() {
      const start = (this.currentPage - 1) * this.perPage
      return this.filteredPosts.slice(start, start + this.perPage)
    },
  },
  watch: {
    search() {
      this.currentPage = 1
    },
    perPage() {
      this.currentPage = 1
    },
  },
  methods: {
    truncate(text, length) {
      return text.length > length ? text.substring(0, length) + '...' : text
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('vi-VN')
    },
    addPost() {
      alert('Đi tới trang thêm bài viết hoặc mở modal')
    },
    editPost(post) {
      alert(`Chỉnh sửa bài viết: ${post.id}`)
    },
    deletePost(post) {
      if (confirm(`Bạn có chắc muốn xoá bài viết "${post.food_name}"?`)) {
        this.posts = this.posts.filter((p) => p.id !== post.id)
      }
    },
  },
}
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
</style>
