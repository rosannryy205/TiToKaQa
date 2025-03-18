<template>
    <div class="container">
        <h2 class="mb-4 fw-bold">Sơ Đồ Bàn</h2>

        <!-- Tabs bộ lọc trạng thái -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active">Tất cả</a>
            </li>
            <li class="nav-item">
                <a class="nav-link">Bàn trống</a>
            </li>
            <li class="nav-item">
                <a class="nav-link">Có khách</a>
            </li>
        </ul>

        <!-- Bộ lọc khu vực -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <label class="me-2">Khu vực:</label>
                <select class="form-select w-auto d-inline-block">
                    <option>Tất cả</option>
                    <option>Ngoài trời</option>
                    <option>Trong phòng</option>
                </select>
            </div>
        </div>

        <!-- Danh sách bàn -->
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <div class="col" v-for="n in 6" :key="n">
                <div class="table-card text-center p-3">
                    <div class="table-number">{{ n }}</div>
                    <p class="status">Bàn trống - 2 người</p>
                    <hr>
                    <p class="area">Ngoài trời</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-card {
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 20px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}
.table-card:hover {
    transform: scale(1.05);
}
.table-number {
    width: 40px;
    height: 40px;
    background: #d12d43;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.status {
    margin-top: 10px;
}
.area{
    color: #6c757d;
}
.nav-tabs .nav-link {
    cursor: pointer;
}
</style>
