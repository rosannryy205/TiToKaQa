<template>
<!-- Form thêm sản phẩm -->
<div class="main-content">
    <a href="/php2/ASMC/admin/product" class="btn btn-dark">Quay về</a>
    <form action="/php2/ASMC/admin/product/insert" method="post" class="fw-semibold" enctype="multipart/form-data">
        <div class="d-flex gap-2 justify-content-around">
            <div class="w-50">
                <label for="name" class="form-label">Tên món ăn</label>
                <input type="text" class="form-control" name="name">
            </div>
            <div class="w-50">
                <label for="price" class="form-label">Giá thành</label>
                <input type="number" class="form-control" id="price" name="price">
            </div>
        </div>
        <div class="d-flex gap-2 justify-content-around">
            <div class="w-50">
                <label for="discount" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="discount" name="discount">
            </div>
        </div>       

        <div class="mb-3">
            <label for="mainImageUpload" class="form-label">Main image</label>
            <input type="file" class="form-control" id="mainImageUpload" name="main_image">
        </div>
        <div id="mainImagePreview" class="mb-3"></div>
        <div class="mb-3">
            <label for="thumbnailUpload" class="form-label">Thumbnail</label>
            <input type="file" name="thumbnails[]" id="thumbnailUpload" class="form-control" multiple>
        </div>
        <div id="thumbnailPreview" class="d-flex flex-wrap"></div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="10"></textarea>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-secondary">Đặt lại</button>
            <button type="submit" class="btn btn-dark" name="insertP">Thêm sản phẩm</button>
        </div>
    </form>
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
