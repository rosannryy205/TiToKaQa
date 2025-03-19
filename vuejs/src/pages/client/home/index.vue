<template>
    <!--main-home-->
    <section class="main d-flex justify-content-center align-items-center">
            <div class="container text-center">
                <div class="row justify-content-center align-items-stretch">
                    <div class="col-4 d-none d-lg-block pt-5 text-start">
                        <div class="title-foods fw-medium fs-5 mt-5">
                            <span>Lẩu và Mỳ cay 7 cấp độ</span>
                        </div>
                        <div class="title-shops d-none d-md-block fw-bold">
                            <span>TITOKAQA</span>
                        </div>
                        <div class="title-infors fw-normal pe-5">
                            <span>TITOKAQA là chuỗi nhà hàng thương hiệu ẩm thực Hàn Quốc ra mắt vào năm 2025.
                                Món “lẩu”, “mỳ cay” với 7 cung bậc cay đã trở thành cơn sốt đối với giới trẻ lúc bấy
                                giờ.
                                TITOKAQA đã trở thành một trong những lựa chọn hàng đầu của giới trẻ Việt Nam khi muốn
                                thưởng thức lẩu nói riêng và ẩm thực Hàn Quốc nói chung.</span>
                        </div>

                        <div class="deals-hot-box d-none d-lg-block">
                            <p class="title">Khám phá ưu đãi hot!</p>
                            <a href="#" class="link">Xem ngay <span>→</span></a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8">
                        <div class="images-foods-banner">
                            <img src="../../../../public/img/Bannerfoods.webp" alt="foods-banner" class="img-fluid">
                        </div>
                    </div>

                </div>
                <!--hôm nay-->
                <div class="mid-banner container-fluid">
                    <img src="../../../../public/img/Banner (3).webp" alt="" class="img-fluid" style="border-radius: 25px;">
                    <button class="trans-left d-none d-lg-block"><i class="fa-solid fa-arrow-left"
                            style="color: #ffffff;"></i></button>
                    <button class="trans-right d-none d-lg-block"><i class="fa-solid fa-arrow-right"
                            style="color: #ffffff;"></i></button>
                </div>
                <section class="foods-homepages d-flex mt-5">
                    <div class="container">
                        <div class="row">
                            <!--Menu -->
                            <div class="col-md-3 d-none d-lg-block">
                                <span class="title-menu fw-bold">THỰC ĐƠN</span>
                                <ul class="menu-list m-5">
                                    <li>
                                        <input type="radio" id="mi-cay" name="menu" checked>
                                        <label for="mi-cay"><i class="fa-solid fa-o"></i> Mì Cay</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="mi-tuong-den" name="menu">
                                        <label for="mi-tuong-den"><i class="fa-solid fa-o"></i> Mì Tương Đen</label>
                                    </li>
                                    <li>
                                        <input type="radio" id="no-ne" name="menu">
                                        <label for="no-ne"><i class="fa-solid fa-o"></i> No Nê</label>
                                    </li>
                                </ul>
                            </div>
                            <!-- small -->
                            <div class="col-12 d-lg-none position-relative">
                                <div class="menu-header d-flex justify-content-between align-items-center"
                                    data-bs-toggle="collapse" data-bs-target="#menuDropdown">
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

                            <div class="col-lg-9 align-items-center text-center">
                                <div class="title-food-menu text-start m-3">
                                    <span class="title-food-menu fw-bold">Mỳ Cay</span>
                                </div>
                                <section class="foods-homepages">
                                    <div class="food-box-left row align-items-center">
                                        <div class="col-md-4 food-image">
                                            <img src="../../../../public/img/food.jpg" alt="Mì Kim Chi Thập Cẩm" class="img-fluid">
                                        </div>
                                        <div class="col-md-8 food-content bg-white text-end">
                                            <h2 class="food-title fw-bold">Mì Kim Chi Thập Cẩm</h2>
                                            <p class="food-price fw-bold">69,000 VNĐ</p>
                                            <p class="food-desc">
                                                Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc...
                                            </p>
                                        </div>
                                    </div>
                                    <div class="food-box-right row align-items-center">
                                        <div class="col-md-8 food-content bg-white text-start">
                                            <h2 class="food-title fw-bold">Mì Kim Chi Thập Cẩm</h2>
                                            <p class="food-price fw-bold">69,000 VNĐ</p>
                                            <p class="food-desc">
                                                Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc...
                                            </p>
                                        </div>
                                        <div class="col-md-4 food-image">
                                            <img src="../../../../public/img/food.jpg" alt="Mì Kim Chi Thập Cẩm" class="img-fluid">
                                        </div>
                                    </div>
                                    <div class="food-box-left row align-items-center">
                                        <div class="col-md-4 food-image">
                                            <img src="../../../../public/img/food.jpg" alt="Mì Kim Chi Thập Cẩm" class="img-fluid">
                                        </div>
                                        <div class="col-md-8 food-content bg-white text-end">
                                            <h2 class="food-title fw-bold">Mì Kim Chi Thập Cẩm</h2>
                                            <p class="food-price fw-bold">69,000 VNĐ</p>
                                            <p class="food-desc">
                                                Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc...
                                            </p>
                                        </div>
                                    </div>
                                    <div class="food-box-right row align-items-center">
                                        <div class="col-md-8 food-content bg-white text-start">
                                            <h2 class="food-title fw-bold">Mì Kim Chi Thập Cẩm</h2>
                                            <p class="food-price fw-bold">69,000 VNĐ</p>
                                            <p class="food-desc">
                                                Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc...
                                            </p>
                                        </div>
                                        <div class="col-md-4 food-image">
                                            <img src="../../../../public/img/food.jpg" alt="Mì Kim Chi Thập Cẩm" class="img-fluid">
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
        <section class="populars-infors-pots">
            <!---->
            <section class="popular-searches container py-4">
                <h2 class="fw-bold mb-3 text-start text-md-start">Nhiều Người Gọi</h2>
                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3">
                    <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
                        <span>🍛</span> Lẩu
                    </span>
                    <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
                        <span>🍜🔥</span> Mỳ cay
                    </span>
                    <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
                        <span>🥩</span> Bò Waygu
                    </span>
                </div>
            </section>

            <!---->
            <section class="pots-section container">
                <h2 class="text-center text-md-start mb-3">NEW POTS <span>📢</span></h2>
                <hr>
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
                    <div class="col">
                        <img src="../../../../public/img/bv1.webp" alt="post1" class="img-fluid rounded">
                    </div>
                    <div class="col">
                        <img src="../../../../public/img/bv2.png" alt="post2" class="img-fluid rounded">
                    </div>
                    <div class="col">
                        <img src="../../../../public/img/bv3.png" alt="post3" class="img-fluid rounded">
                    </div>
                    <div class="col">
                        <img src="../../../../public/img/bv1.webp" alt="post4" class="img-fluid rounded">
                    </div>
                </div>
            </section>
        </section>
</template>