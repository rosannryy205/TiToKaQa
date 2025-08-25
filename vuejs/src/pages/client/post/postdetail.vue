<template>
  <div class="container py-5">
    <div v-if="post" class="card border-0 shadow-lg rounded-4 overflow-hidden">
      <!-- Ảnh bài viết -->
      <img
        :src="`http://127.0.0.1:8000/storage/img/post/${post.image}`"
        :alt="post.title"
        class="post-image"
      />

      <!-- Nội dung -->
      <div class="card-body p-4 p-md-5">
        <!-- Danh mục + ngày -->
        <div class="d-flex align-items-center text-muted mb-3 small">
          <span class="badge bg-primary me-2">{{ post.category }}</span>
          <span>
            <i class="bi bi-calendar-event me-1"></i>
            {{ formatDate(post.published_at) }}
          </span>
        </div>

        <!-- Tiêu đề -->
        <h1 class="fw-bold mb-4">{{ post.title }}</h1>

        <!-- Nội dung markdown -->
        <div v-html="parsedContent" class="post-content"></div>
      </div>
    </div>

    <div v-else class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>
  </div>
</template>

<script>
import { marked } from "marked";
import router from "@/router";

export default {
  data() {
    return {
      post: null,
      parsedContent: "",
    };
  },
  mounted() {
    this.fetchPost();
  },
  methods: {
    async fetchPost() {
      try {
<<<<<<< HEAD
        const postId = router.currentRoute.value.params.id; // lấy id từ URL
        const res = await fetch(`http://127.0.0.1:8000/api/get_post/${postId}`);
=======
        const API_URL = "http://127.0.0.1:8000/api"
        const postId = router.currentRoute.value.params.id; // lấy id từ URL
        const res = await fetch(`${API_URL}/get_post/${postId}`);
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
        const data = await res.json();
        this.post = data.data;
        this.parsedContent = marked.parse(this.post.content);
      } catch (err) {
        console.error(err);
      }
    },
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
  },
};
</script>


<style scoped>
.post-image {
  width: 100%;
  max-height: 450px;
  object-fit: cover;
}

.post-content {
  font-size: 1.1rem;
  line-height: 1.8;
}

.post-content h2,
.post-content h3 {
  margin-top: 1.5rem;
}

.post-content img {
  max-width: 100%;
  border-radius: 8px;
  margin: 1rem 0;
}
</style>
