<template>
      <section class="foods">
      <div class="main-page-foods container">
        <div class="row d-flex align-items-center justify-content-between">
          <div class="col-md-6 d-none d-lg-flex justify-content-center align-items-center">
            <span class="fw-bold text-center align-items-center justify-content-center">Thực đơn</span>
          </div>
          <div class="col-6 d-none d-lg-flex flex-wrap justify-content-center ps-5">
            <ul class="menu-list m-5 d-flex">
              <li>
                <input type="radio" id="mi-cay" name="menu" checked />
                <label for="mi-cay"><i class="fa-solid fa-o"></i> Mì Cay</label>
              </li>
              <li>
                <input type="radio" id="mi-tuong-den" name="menu" />
                <label for="mi-tuong-den"><i class="fa-solid fa-o"></i> Mì Tương Đen</label>
              </li>
              <li>
                <input type="radio" id="no-ne" name="menu" />
                <label for="no-ne"><i class="fa-solid fa-o"></i> No Nê</label>
              </li>
            </ul>
            <ul class="menu-list m-5 d-flex">
              <li>
                <input type="radio" id="top-ping" name="menu" checked />
                <label for="top-ping"><i class="fa-solid fa-o"></i>Toping</label>
              </li>
              <li>
                <input type="radio" id="giai-khat" name="menu" />
                <label for="giai-khat"><i class="fa-solid fa-o"></i>Giải Khát</label>
              </li>
            </ul>
          </div>
        </div>
        <!--small-->
        <div class="col-12 d-lg-none position-relative mt-5">
          <div
            class="menu-header d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            data-bs-target="#menuDropdown"
          >
            <h2 class="menu-title">Thực đơn</h2>
            <div class="menu-icon d-flex align-items-center">
              <i class="fas fa-list-alt"></i>
              <span>Danh mục</span>
            </div>
          </div>
          <div id="menuDropdown" class="collapse menu-dropdown">
            <ul class="list-group mb-3">
              <li class="list-group-item">Mì Cay</li>
              <li class="list-group-item">Lẩu Hàn Quốc</li>
              <li class="list-group-item">Cơm Trộn</li>
              <li class="list-group-item">Đồ Ăn Kèm</li>
            </ul>
          </div>
        </div>
      </div>
      <!---->
      <div class="background-banner"></div>
      <section class="all-dish-by-category">
        <div class="dish-by-category">
          <div class="row custom-row">
            <div class="col-12 images-dish d-flex justify-content-between">
              <div class="col-8 img-dish">
                <img src="../../../../public/img/food.jpg" alt="dish-images" width="300px" />
              </div>
              <div class="col-4 title-dish">
                <p class="text-start">Mỳ Cay</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card" data-bs-toggle="modal" data-bs-target="#productModal">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="product-card">
                <img
                  src="../../../../public/img/food.jpg"
                  alt="Mì Kim Chi Thập Cẩm"
                  class="product-img"
                  width="180px"
                />
                <h3 class="product-dish-title text-center">Mì Kim Chi Thập Cẩm</h3>
                <p class="product-dish-desc text-start">
                  Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm...
                </p>
                <p class="product-dish-price fw-bold">69,000 VNĐ</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>

    <section class="banner-deals">
      <div class="banner-deals container-fluid">
        <img src="../../../../public/img/Banner (3).webp" alt="" class="img-fluid" style="border-radius: 25px" />
        <button class="trans-left d-none d-lg-block">
          <i class="fa-solid fa-arrow-left" style="color: #ffffff"></i>
        </button>
        <button class="trans-right d-none d-lg-block">
          <i class="fa-solid fa-arrow-right" style="color: #ffffff"></i>
        </button>
      </div>
    </section>
    <!---->

    <section class="pots-section container">
      <h2 class="text-center text-md-start mb-3 fw-bold">Bài Viết & Thông Tin<span>📢</span></h2>
      <hr />
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <div class="col">
          <img src="../../../../public/img/bv1.webp" alt="post1" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv2.png" alt="post2" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv3.png" alt="post3" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv1.webp" alt="post4" class="img-fluid rounded" />
        </div>
      </div>
    </section>
    <!-- Modal -->
    <div
      class="modal fade"
      id="productModal"
      tabindex="-1"
      aria-labelledby="productModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content custom-modal">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-5 text-center">
                <img src="../../../../public/img/food.jpg" alt="Mì Cay Lẩu" width="100%" />
              </div>
              <div class="col-md-7 d-flex flex-column justify-content-center">
                <h4 class="fw-bold">🔥 Mì Cay Lẩu Hải Sản 🔥</h4>
                <p class="fw-bold text-dark">
                  <i class="fa-solid fa-star" style="color: #ffd43b"></i> 4.8/5 | 125 đánh giá
                </p>
                <p class="text-danger fw-bold fs-4">79.000đ</p>

                <p class="text-secondary">
                  Thưởng thức vị cay nồng với sự kết hợp hoàn hảo của
                  <strong>tôm, mực, ngao</strong> và nước lẩu đậm đà!
                </p>
                <div class="mb-3">
                  <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                  <select class="form-select" id="spicyLevel">
                    <option value="1">Cấp độ 1 - Nhẹ</option>
                    <option value="2">Cấp độ 2 - Trung bình</option>
                    <option value="3">Cấp độ 3 - Cay</option>
                    <option value="4">Cấp độ 4 - Siêu Cay</option>
                  </select>
                </div>
                <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>