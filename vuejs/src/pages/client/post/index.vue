<template>
  <section class="container py-4 py-md-5">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
      <div class="header-info flex-equal">
        <h2 class="h3 h-md-2 fw-bold mb-1">📄 Bài viết mới</h2>
        <p class="text-secondary mb-0">
          Cập nhật mọi thông tin về chính sách ưu đãi
        </p>
      </div>

      <!-- Controls -->
      <div class="d-flex flex-wrap gap-2 equal-inputs flex-equal">
        <div class="search-group">
          <span class="search-icon">
            <i class="bi bi-search"></i>
          </span>
          <input v-model.trim="filters.search" @input="debouncedFetch()" type="search" class="search-input"
            placeholder="Tìm kiếm..." />
        </div>

        <select v-model="filters.category" @change="fetchPosts()" class="form-select">
          <option value="">Tất cả danh mục</option>
          <option v-for="c in categories" :key="c.value" :value="c.value">
            {{ c.label }}
          </option>
        </select>

        <select v-model="filters.sort" @change="fetchPosts()" class="form-select">
          <option value="-published_at">Mới nhất</option>
          <option value="published_at">Cũ nhất</option>
          <option value="-views">Lượt xem cao</option>
          <option value="-likes">Lượt thích cao</option>
        </select>

        <select v-model="filters.limit" @change="fetchPosts()" class="form-select">
          <option :value="6">6 / trang</option>
          <option :value="9">9 / trang</option>
          <option :value="12">12 / trang</option>
        </select>

      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="row g-3 g-md-4">
      <div v-for="n in 6" :key="n" class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm placeholder-glow">
          <div class="ratio ratio-16x9 bg-light rounded-top"><span class="placeholder w-100 h-100"></span></div>
          <div class="card-body">
            <h3 class="card-title h6"><span class="placeholder col-8"></span></h3>
            <p class="card-text mb-2"><span class="placeholder col-12"></span></p>
            <p class="card-text mb-3"><span class="placeholder col-10"></span></p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="placeholder col-4"></span>
              <span class="placeholder col-2"></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="alert alert-danger d-flex align-items-center" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div class="me-auto">{{ error }}</div>
      <button class="btn btn-sm btn-light ms-3" @click="fetchPosts()">
        Thử lại
      </button>
    </div>

    <!-- Empty state -->
    <div v-else-if="!paginatedPosts.length" class="text-center py-5">
      <i class="bi bi-inboxes fs-1 d-block mb-2 text-secondary"></i>
      <p class="text-secondary mb-3">Chưa có bài viết nào phù hợp.</p>
      <button class="btn btn-primary" @click="resetFilters">Xoá bộ lọc</button>
    </div>


    <!-- Grid -->
    <div v-else class="row g-3 g-md-4">

      <article v-for="post in paginatedPosts" :key="post.id" class="col-12 col-sm-6 col-lg-4">
        <router-link :to="`/posts_detail/${post.id}`">
          <div class="card h-100 border-0 shadow-sm">
            <div class="position-relative">
              <a :href="postUrl(post)" class="text-decoration-none">
                <div class="ratio ratio-16x9 bg-light">
                  <img v-if="post.image" :src="getImageUrl(post.image)" :alt="post.title"
                    class="img-fluid object-fit-cover rounded-top" loading="lazy" />
                  <div v-else class="d-flex align-items-center justify-content-center bg-light rounded-top">
                    <i class="bi bi-image text-secondary"></i>
                  </div>
                </div>
              </a>
              <span v-if="post.badge" class="badge bg-primary position-absolute top-0 start-0 m-2 rounded-pill">
                {{ post.badge }}
              </span>
            </div>

            <div class="card-body d-flex flex-column">
              <a :href="postUrl(post)" class="stretched-link text-decoration-none text-dark">
                <h3 class="card-title h5 text-truncate-2 mb-2">{{ post.title }}</h3>
              </a>
              <!-- Bỏ truncate -->
              <div class="d-flex flex-wrap gap-2 small text-secondary mt-auto">
                <span><i class="bi bi-calendar3 me-1"></i>{{ formatDate(post.published_at || post.created_at) }}</span>
                <span v-if="post.category"><i class="bi bi-tag me-1"></i>{{ post.category }}</span>
              </div>
            </div>
          </div>
        </router-link>
      </article>
    </div>

    <!-- Pagination -->
    <nav v-if="lastPage > 1" class="mt-4 mt-md-5" aria-label="Pagination">
      <ul class="pagination justify-content-center flex-wrap gap-2">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">«</button>
        </li>
        <li v-for="p in pageList" :key="p.key" class="page-item"
          :class="{ active: p.num === currentPage, disabled: p.ellipsis }">
          <button class="page-link" @click="p.ellipsis ? null : goToPage(p.num)" :disabled="p.ellipsis">{{ p.label
            }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
          <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage">»</button>
        </li>
      </ul>
    </nav>
  </section>
</template>


<script setup>
import axios from "axios";
import { ref, computed, onMounted } from "vue";
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'


const getImageUrl = (image) => `${STORAGE_URL}/img/food/${image}`
// Filters
const filters = ref({
  search: "",
  category: "",
  sort: "-published_at",
  limit: 6,
});

// Categories
const categories = [
  { value: "Tin tức", label: "Tin tức" },
  { value: "Món ăn", label: "Món ăn" },
  { value: "Khuyến mãi", label: "Khuyến mãi" },
];

// State
const loading = ref(false);
const error = ref("");
const allPosts = ref([]);
const currentPage = ref(1);

// Helpers
const postUrl = (post) => `/posts/${post.slug || post.id}`;
const n = (v) => new Intl.NumberFormat().format(Number(v || 0));
const formatDate = (d) => {
  if (!d) return "—";
  const dt = new Date(d);
  return new Intl.DateTimeFormat("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  }).format(dt);
};

// Fetch tất cả bài viết
const fetchPosts = async () => {
  loading.value = true;
  error.value = "";
  try {
    const res = await axios.get(`${API_URL}/get_all_post`);
    let posts = [];
    if (Array.isArray(res.data.result)) {
      posts = res.data.result;
    } else {
      posts = res.data.result.data || [];
    }
    // Lọc chỉ lấy bài viết không ẩn
    allPosts.value = posts.filter(post => post.is_hidden === 0);
    console.log(allPosts.value);
  } catch (err) {
    error.value = "Không thể tải dữ liệu";
  } finally {
    loading.value = false;
  }
};


// Lọc, tìm kiếm, sắp xếp
const filteredPosts = computed(() => {
  let result = [...allPosts.value];

  // Tìm kiếm
  if (filters.value.search) {
    const keyword = filters.value.search.toLowerCase();
    result = result.filter(
      (p) =>
        p.title?.toLowerCase().includes(keyword) ||
        p.content?.toLowerCase().includes(keyword)
    );
  }

  // Lọc theo category
  if (filters.value.category) {
    result = result.filter((p) => p.category === filters.value.category);
  }

  // Sắp xếp
  if (filters.value.sort) {
    const key = filters.value.sort.replace("-", "");
    const desc = filters.value.sort.startsWith("-");
    result.sort((a, b) => {
      if (a[key] < b[key]) return desc ? 1 : -1;
      if (a[key] > b[key]) return desc ? -1 : 1;
      return 0;
    });
  }

  return result;
});

// Tính toán dữ liệu hiển thị theo trang
const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * filters.value.limit;
  const end = start + filters.value.limit;
  return filteredPosts.value.slice(start, end);
});

// Tổng số trang
const lastPage = computed(() =>
  Math.ceil(filteredPosts.value.length / filters.value.limit)
);

// Chuyển trang
const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    currentPage.value = page;
  }
};

// Reset filters
const resetFilters = () => {
  filters.value = {
    search: "",
    category: "",
    sort: "-published_at",
    limit: 6,
  };
  currentPage.value = 1;
};

onMounted(() => {
  fetchPosts();
});
</script>





<style>
/* Container input + selects */
.equal-inputs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  /* khoảng cách giữa các input */
  width: 100%;
}

.equal-inputs>* {
  flex: 1 1 200px;
  /* mỗi item có thể co giãn, min-width 200px */
  min-width: 0;
}

/* Search group */
.search-group {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fff;
  padding: 0.25rem 0.5rem;
  transition: border 0.2s, box-shadow 0.2s;
}

.search-group:hover {
  border-color: #0d6efd;
  /* xanh dương khi hover */
  box-shadow: 0 0 5px rgba(13, 110, 253, 0.2);
}

.search-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 0.5rem;
  color: #6c757d;
  font-size: 1.1rem;
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 0.4rem 0.5rem;
  font-size: 0.95rem;
}

/* Selects */
.equal-inputs select {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 0.4rem 0.5rem;
  font-size: 0.95rem;
  transition: border 0.2s, box-shadow 0.2s;
  background-color: #fff;
  color: #495057;
}

.equal-inputs select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 5px rgba(13, 110, 253, 0.2);
  outline: none;
}

/* Responsive nhỏ hơn 576px */
@media (max-width: 575.98px) {
  .equal-inputs>* {
    flex: 1 1 100%;
    /* mỗi input/ select full width */
  }
}

.header-info {
  max-width: 350px;
  width: 100%;
}
</style>
