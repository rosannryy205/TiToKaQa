<template>
    <h2 class="mb-3">Quản lý món ăn</h2>

    <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            +Thêm món ăn
        </button>
        <span class="vd">Tìm kiếm</span>
        <input type="text" class="form-control" style="max-width: 200px" placeholder="Tìm kiếm món ăn" />
        <span class="vd">Lọc</span>
        <select class="form-select w-auto" style="max-width: 250px">
            <option selected>Lọc theo danh mục cha</option>
            <option>Danh mục 1</option>
            <option>Danh mục 2</option>
        </select>


        <span class="vd">Hiển thị</span>
        <select class="form-select w-auto">
            <option selected>5</option>
            <option>10</option>
            <option>15</option>
        </select>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" /></th>
                    <th>Món ăn</th>
                    <th>Danh mục</th>
                    <th>Giá thành</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" /></td>
                    <td><img src="/img/food/mykimchihaisan.webp" alt="Coca Cola" class="me-2 img_thumbnail" /> Mỳ kim chi hải sản</td>
                    <td>Mỳ cay</td>
                    <td>55,000 VNĐ</td>
                    <td>10</td>
                    <td class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary">Sửa</button>
                        <button class="btn btn-danger">Xoá</button>
                        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#productModal">Biến thể</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <button class="btn btn-danger">Xoá</button>


     <!-- Modal -->
     <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h5 class="modal-title" id="productModalLabel">Danh sách biến thể</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        +Thêm biến thể
                    </button>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" /></th>
                                    <th>Món ăn</th>
                                    <th>Giá thành</th>
                                    <th>Cấp độ</th>
                                    <th>Số lượng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" /></td>
                                    <td><img src="/img/food/mykimchihaisan.webp" alt="Coca Cola" class="me-2 img_thumbnail" /> Mỳ kim chi hải sản</td>
                                    <td>55,000 VNĐ</td>
                                    <td>Cấp 1</td>
                                    <td>10</td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-primary">Sửa</button>
                                        <button class="btn btn-danger">Xoá</button>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                        <button class="btn btn-danger">Xoá</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { useMenu } from '@/stores/use-menu'
export default {
    setup() {
        useMenu().onSelectedKeys(['admin-roles'])
    },
}
</script>

<style scoped>
.img_thumbnail{
    width: 50px;
}
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    .vd{
        display: none;
    }
}
</style>