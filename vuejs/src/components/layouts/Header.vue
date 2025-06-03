<template>
  <!-- top header-->
  <div class="header position-sticky top-0 bg-white bg-opacity-90 shadow-sm z-3">
      <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="container-fluid px-0">
            <div class="d-flex align-items-center">
              <button class="navbar-toggler me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"

              aria-controls="offcanvasMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <a class="navbar-brand" href="/home">
                <img src="/img/logonew.png" alt="Logo" class="logo" width="80px">
              </a>
            </div>

          <div class="d-none d-lg-flex align-items-center ms-auto">
            <form @submit.prevent="searchProduct" class="me-3">
              <div class="input-wrapper position-relative" ref="wrapperRef">
                <button class="icon-search-submit" type="submit">
                  <svg width="23px" height="23px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                      stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round"></path>
                  </svg>
                </button>
                <input v-model="searchQuery" type="text" class="input-search" placeholder="search..."
                  @input="handleInput" @focus="() => { handleInput(); showSuggestions = true; }"
                  @keydown.enter="searchProduct" />
                <ul v-if="suggestions.length && showSuggestions" class="suggestion-dropdown"
                  @scroll.passive="handleScroll">
                  <li v-for="(item, index) in suggestions" :key="index" @click="selectItem(item)">
                    {{ item.name }}
                  </li>
                  <li v-if="loading" class="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span> Đang tải thêm...
                  </li>
                  <li v-if="!hasMore && !loading" class="no-more">Đã hết kết quả</li>
                </ul>
              </div>
            </form>

            <div class="me-3">
              <button v-if="!isLoggedIn" class="icon-btn" data-bs-toggle="modal" @click="openLoginModal"
                title="Đăng nhập">
                <i class="bi bi-people"></i>
              </button>
              <template v-else>
                <div class="d-flex align-items-center">
                  <router-link to="/update-user" class="text-decoration-none text-primary-red me-2">
                    <p v-if="user.username" class="mb-0 username-display">{{ user.username }}</p>
                  </router-link>
                  <button class="icon-btn" @click="handleLogout" title="Đăng xuất">
                    <i class="bi bi-box-arrow-right"></i> </button>
                </div>
              </template>
            </div>

            <div class="d-none d-lg-block">
              <router-link to="/delivery" style="color: black;">
                <div class="loader">
                  <div class="truckWrapper">
                    <div class="truckBody">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 198 93" class="trucksvg">
                        <path stroke-width="3" stroke="#282828" fill="#F83D3D"
                          d="M135 22.5H177.264C178.295 22.5 179.22 23.133 179.594 24.0939L192.33 56.8443C192.442 57.1332 192.5 57.4404 192.5 57.7504V89C192.5 90.3807 191.381 91.5 190 91.5H135C133.619 91.5 132.5 90.3807 132.5 89V25C132.5 23.6193 133.619 22.5 135 22.5Z">
                        </path>
                        <path stroke-width="3" stroke="#282828" fill="#7D7C7C"
                          d="M146 33.5H181.741C182.779 33.5 183.709 34.1415 184.078 35.112L190.538 52.112C191.16 53.748 189.951 55.5 188.201 55.5H146C144.619 55.5 143.5 54.3807 143.5 53V36C143.5 34.6193 144.619 33.5 146 33.5Z">
                        </path>
                        <path stroke-width="2" stroke="#282828" fill="#282828"
                          d="M150 65C150 65.39 149.763 65.8656 149.127 66.2893C148.499 66.7083 147.573 67 146.5 67C145.427 67 144.501 66.7083 143.873 66.2893C143.237 65.8656 143 65.39 143 65C143 64.61 143.237 64.1344 143.873 63.7107C144.501 63.2917 145.427 63 146.5 63C147.573 63 148.499 63.2917 149.127 63.7107C149.763 64.1344 150 64.61 150 65Z">
                        </path>
                        <rect stroke-width="2" stroke="#282828" fill="#FFFCAB" rx="1" height="7" width="5" y="63"
                          x="187">
                        </rect>
                        <rect stroke-width="2" stroke="#282828" fill="#282828" rx="1" height="11" width="4" y="81"
                          x="193"></rect>
                        <rect stroke-width="3" stroke="#282828" fill="#DFDFDF" rx="2.5" height="90" width="121" y="1.5"
                          x="6.5">
                        </rect>
                        <rect stroke-width="2" stroke="#282828" fill="#DFDFDF" rx="2" height="4" width="6" y="84" x="1">
                        </rect>
                      </svg>
                    </div>
                    <div class="truckTires">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                        <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                        <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                      </svg>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                        <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15"></circle>
                        <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                      </svg>
                    </div>
                    <div class="road"></div>

                    <svg xml:space="preserve" viewBox="0 0 453.459 453.459" xmlns:xlink="http://www.w3.org/1999/xlink"
                      xmlns="http://www.w3.org/2000/svg" id="Capa_1" version="1.1" fill="#000000" class="lampPost">
                      <path d="M252.882,0c-37.781,0-68.686,29.953-70.245,67.358h-6.917v8.954c-26.109,2.163-45.463,10.011-45.463,19.366h9.993
  c-1.65,5.146-2.507,10.54-2.507,16.017c0,28.956,23.558,52.514,52.514,52.514c28.956,0,52.514-23.558,52.514-52.514
  c0-5.478-0.856-10.872-2.506-16.017h9.992c0-9.354-19.352-17.204-45.463-19.366v-8.954h-6.149C200.189,38.779,223.924,16,252.882,16
  c29.952,0,54.32,24.368,54.32,54.32c0,28.774-11.078,37.009-25.105,47.437c-17.444,12.968-37.216,27.667-37.216,78.884v113.914
  h-0.797c-5.068,0-9.174,4.108-9.174,9.177c0,2.844,1.293,5.383,3.321,7.066c-3.432,27.933-26.851,95.744-8.226,115.459v11.202h45.75
  v-11.202c18.625-19.715-4.794-87.527-8.227-115.459c2.029-1.683,3.322-4.223,3.322-7.066c0-5.068-4.107-9.177-9.176-9.177h-0.795
  V196.641c0-43.174,14.942-54.283,30.762-66.043c14.793-10.997,31.559-23.461,31.559-60.277C323.202,31.545,291.656,0,252.882,0z
  M232.77,111.694c0,23.442-19.071,42.514-42.514,42.514c-23.442,0-42.514-19.072-42.514-42.514c0-5.531,1.078-10.957,3.141-16.017
  h78.747C231.693,100.736,232.77,106.162,232.77,111.694z"></path>
                    </svg>
                  </div>
                </div>
              </router-link>
            </div>

            <div>
              <router-link to="/cart" class="icon-btn text-dark" title="Giỏ hàng">
                <i class="bi bi-cart"></i>
              </router-link>
            </div>
          </div>

        </div>
      </nav>

      <nav class="navbar navbar-expand-lg navbar-bottom d-none d-lg-block pt-0">
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav main-nav-links">
            <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
          </ul>
        </div>
      </nav>
    </div>
      <nav class="navbar navbar-expand-lg navbar-bottom d-none d-lg-block pt-0">
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav main-nav-links">
            <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav offcanvas-nav-links mb-4">
          <li class="nav-item"><router-link class="nav-link" to="/home">Trang chủ</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/food">Thực đơn</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/reservation">Đặt bàn</router-link></li>
        </ul>
        </ul>

        <div class="mobile-actions">
          <div class="input-wrapper position-relative mb-3">
            <button class="icon-search-submit" type="button"> <svg width="23px" height="23px" viewBox="0 0 24 24"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                  stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                </path>
              </svg>
            </button>
            <input type="text" class="input-search" placeholder="search..." />
          </div>
        <div class="mobile-actions">
          <div class="input-wrapper position-relative mb-3">
            <button class="icon-search-submit" type="button"> <svg width="23px" height="23px" viewBox="0 0 24 24"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z"
                  stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                </path>
              </svg>
            </button>
            <input type="text" class="input-search" placeholder="search..." />
          </div>

          <div class="d-flex flex-column align-items-start">
          <div class="d-flex flex-column align-items-start">
            <button v-if="!isLoggedIn" class="icon-btn text-dark mb-2" data-bs-toggle="modal" @click="openLoginModal">
              <i class="bi bi-people me-2"></i> Đăng nhập
              <i class="bi bi-people me-2"></i> Đăng nhập
            </button>
            <template v-else>
              <div class="mb-2">
                <router-link to="/update-user" class="text-decoration-none text-primary-red me-2">
                  <p v-if="user.username" class="mb-0 username-display"><i class="bi bi-person me-2"></i>{{
                    user.username }}</p>
                </router-link>
              </div>
              <button class="icon-btn text-dark mb-2" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
              </button>
              <div class="mb-2">
                <router-link to="/update-user" class="text-decoration-none text-primary-red me-2">
                  <p v-if="user.username" class="mb-0 username-display"><i class="bi bi-person me-2"></i>{{
                    user.username }}</p>
                </router-link>
              </div>
              <button class="icon-btn text-dark mb-2" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
              </button>
            </template>

            <router-link to="/delivery" class="icon-btn text-dark mb-2">
              <i class="bi bi-truck me-2"></i> Theo dõi đơn hàng
            </router-link>
            <router-link to="/cart" class="icon-btn text-dark mb-2">
              <i class="bi bi-cart me-2"></i> Giỏ hàng
            </router-link>
            <a href="tel:YOUR_PHONE_NUMBER" class="icon-btn text-dark"> <i class="bi bi-telephone me-2"></i> Liên hệ
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal đăng nhập -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title w-100 text-center fw-bold" id="loginModalLabel">Đăng nhập</h5>
        </div>
        <div class="modal-body px-4 py-3">
          <form @submit.prevent="handleLogin">
            <div v-if="loginError" class="text-danger small text-center">{{ loginError }}</div>
            <div></div>

            <!-- <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" class="form-control" v-model="loginData.login" placeholder="Tên đăng nhập hoặc email">
            </div> -->

            <div class="mb-3 position-relative">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" placeholder="Tên đăng nhập hoặc email"
                v-model="loginData.login">

            </div>

            <!-- <div class="mb-3 position-relative input-group">
              <span class="input-icon">
                <i class="bi bi-lock"></i>
              </span>
              <input type="password" class="form-control" v-model="loginData.password" id="password"
                placeholder="Nhập mật khẩu">
            </div> -->

            <div class="mb-3 position-relative">

              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" placeholder="Mật khẩu" v-model="loginData.password">

            </div>


            <div class="mb-3 d-flex justify-content-end gap-3 small">
              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
                class="text-decoration-none">Quên mật khẩu</a>

              <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal"
                class="text-decoration-none">Đăng ký</a>
            </div>
            <div class="mb-3">

              <button type="submit" class="btn btn-login w-100" :disabled="loading"> <span v-if="loading"
                  class="spinner-border spinner-border-sm me-2"></span>Đăng nhập</button>
            </div>

            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc đăng nhập</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button @click="loginWithGoogle" type="button" class="btn btn-social"><i
                  class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="registerModalLabel">Đăng ký</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="Handleregister">
            <!-- Username -->
            <div v-if="registerErrors.username" class="text-danger small text-center" style="min-height: 16px;">{{
              registerErrors.username[0] }}</div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative ">

              <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" placeholder="Tên đăng nhập"
                v-model="registerData.username">

            </div>

            <!-- Email -->
            <div v-if="registerErrors.email" class="text-danger small text-center error-message">{{
              registerErrors.email[0]
              }}
            </div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative">

              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" placeholder="Email"
                v-model="registerData.email">

            </div>

            <!-- Phone  -->
            <div v-if="registerErrors.phone" class="text-danger small text-center error-message">{{
              registerErrors.phone[0]
              }}
            </div>
            <div v-else style="height:3px"></div>
            <div class="mb-3 position-relative">
              <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5 register-input" id="phone" placeholder="Số điện thoại"
                v-model="registerData.phone">
            </div>

            <!-- Password -->
            <div v-if="registerErrors.password" class="text-danger small text-center error-message">{{
              registerErrors.password[0] }}</div>
            <div v-else style="height:10px"></div>
            <div class="mb-3 position-relative">

              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5 register-input" placeholder="Mật khẩu"
                v-model="registerData.password">

            </div>

            <!-- Confirm Password -->
            <div style="height:3px"></div>
            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5 register-input" placeholder="Nhập lại mật khẩu"
                v-model="registerData.password_confirmation">
            </div>

            <!-- Đăng ký -->
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Đăng ký
              </button>
            </div>

            <!-- Chuyển sang đăng nhập -->
            <div class="mb-3 text-end">
              <a href="#" data-bs-toggle="modal" @click="openLoginModal">Đã có tài
                khoản</a>
            </div>

            <div class="divider d-flex align-items-center mb-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">hoặc đăng nhập</span>
              <hr class="flex-grow-1">
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button type="button" class="btn btn-social" @click="loginWithGoogle"><i
                  class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social" @click="loginWithGoogle"><i
                  class="bi bi-google"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-facebook"></i></button>
              <button type="button" class="btn btn-social"><i class="bi bi-twitter-x"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>




  <!-- Quên mật khẩu  -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Quên mật khẩu</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="forgotPass">

            <!-- nhập email  -->
            <div v-if="errorSendCode" class="text-danger small text-center">{{ errorSendCode }}</div>
            <div class="mb-3 position-relative">
              <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="text" class="form-control ps-5" id="email" placeholder="Nhập Email" v-model=verify.email>
            </div>

            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Gửi
              </button>
              <!-- data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#authenticationModal" -->
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- nhập code  -->
  <div class="modal fade" id="authenticationModal" tabindex="-1" aria-labelledby="authenticationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="authenticationModalLabel">Nhập mã xác nhận</h5>
        </div>
        <div class="modal-body">
          <form @submit.prevent="verifyResetCode">
            <div class="mb-3 d-flex justify-content-center gap-3 small">
              <p v-if="!errorVerify" class="text-success text-center">
                Mã xác nhận đã được gửi về email. Vui lòng kiểm tra email.
              </p>

              <!-- Thông báo lỗi -->
              <p v-if="errorVerify" class="text-danger text-center">
                {{ errorVerify }}
              </p>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3 position-relative">
              <input v-for="(digit, index) in codeDigits" :key="index" v-model="codeDigits[index]" type="text"
                maxlength="1" class="form-control text-center border-black fw-bold fs-4"
                style="width: 50px; height: 50px;" @input="moveToNext(index, $event)"
                @keydown.backspace="handleBackspace($event, index)" @compositionstart="isComposing = true"
                @compositionend="isComposing = false" ref="inputs" />
            </div>
            <div class=" d-flex justify-content-center gap-3 small">
              <!-- <a href="#" class="text-decoration-none" > -->
              <!-- :class="{ 'disabled': isCounting }" @click="startCountdown" -->
              <p class="text-primary text-decoration-underline" style="cursor: pointer" @click="sendCode"
                v-if="!loadingSend && wait === 0">
                Gửi lại mã
              </p>

              <p class="text-muted" v-else-if="wait > 0">
                Gửi lại mã ({{ wait }}s)
              </p>

              <p class="text-muted" v-else>
                Đang gửi...
              </p>
              <!-- </a> -->
            </div>
            <div class="mb-3 d-flex justify-content-center align-items-center gap-2 small">
              <i class="bi bi-clock text-danger"></i>
              <p class="mb-0 fw-bold text-danger">
                {{ minutes.toString().padStart(2, '0') }}:{{ seconds.toString().padStart(2, '0') }}
              </p>
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác nhận
              </button>
              <!-- ata-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#resetModal" -->
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="resetModalLabel">Đặt lại mật khẩu</h5>
        </div>
        <div class="modal-body">

          <form @submit.prevent="ResetPass">
            <div v-if="errorResetPass" class="text-danger text-center small">{{ errorResetPass }}</div>
            <div class="mb-3 position-relative">
              <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password" placeholder="Mật khẩu"
                v-model="verify.password">
            </div>

            <div class="mb-3 position-relative">
              <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
              <input type="password" class="form-control ps-5" id="password_confirm" placeholder="Nhập lại mật khẩu"
                v-model="verify.password_confirmation">
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-login form-control fw-semibold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Xác nhận
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <router-view></router-view>

</template>
<script setup>
import { useAuthStore } from '@/stores/auth';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { reactive, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import * as bootstrap from 'bootstrap';
import { toast } from 'vue3-toastify';
// const { formattedTime, isCounting, startCountdown } = useCountdown(60);
const auth = useAuthStore();
//Google
const loginWithGoogle = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect';
};

const router = useRouter();
window.bootstrap = bootstrap;

// biến để hiển thị countdown
const minutes = ref(0);
const seconds = ref(0);
let countdownInterval = null;

const startCountdown = (expireTime) => {
  clearInterval(countdownInterval); // Xóa interval cũ nếu có

  const target = expireTime; // Thời gian hết hạn (5 phút từ lúc gửi request)
  const now = new Date().getTime(); // Thời gian hiện tại lúc nhận được dữ liệu
  let remainingTime = target - now; // Tính toán thời gian còn lại

  // Nếu thời gian còn lại đã hết, không cần bắt đầu countdown
  if (remainingTime <= 0) {
    minutes.value = 0;
    seconds.value = 0;
    return;
  }

  // Cập nhật thời gian ban đầu (phút và giây)
  minutes.value = Math.floor(remainingTime / 60000); // Phút
  seconds.value = Math.floor((remainingTime % 60000) / 1000); // Giây

  countdownInterval = setInterval(() => {
    remainingTime -= 1000; // Mỗi giây trôi qua, giảm đi 1 giây

    if (remainingTime <= 0) {
      clearInterval(countdownInterval); // Dừng countdown khi hết thời gian
      minutes.value = 0;
      seconds.value = 0;
      return;
    }

    // Cập nhật lại phút và giây sau mỗi giây
    minutes.value = Math.floor(remainingTime / 60000);
    seconds.value = Math.floor((remainingTime % 60000) / 1000);
  }, 1000);
};



// function stopCountdown() {
//   clearInterval(timer);
// }

// tạo thông tin register
const registerData = reactive({
  username: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
});

// tạo thông tin login

const loginData = reactive({
  login: '',
  password: ''
});
// tạo biến báo lỗi đăng ký
const registerErrors = reactive({});
const firstErrorKey = ref('');

// tạo hiệu ứng load
const loading = ref(false);
const loadingSend = ref(false);

// kiểm tra đã đăng nhập chưa
const user = ref(JSON.parse(localStorage.getItem('user')) || null);
const isLoggedIn = ref(!!user.value);
onMounted(() => {
  const storedUser = JSON.parse(localStorage.getItem('user'));
  user.value = storedUser;
  isLoggedIn.value = !!storedUser

});


// tạo thông tin quên mật khẩu
const codeDigits = ref(['', '', '', '', '', ''])
const isComposing = ref(false);
const inputs = ref([]);

const verify = reactive({
  email: '',
  password: '',
  password_confirmation: ''
})


// báo lỗi nhập mã
const errorSendCode = ref('');
const errorVerify = ref('');
const wait = ref(0);
// tạo biến báo lỗi login
const loginError = ref('');
const loginErrors = reactive({});

// lỗi đặt lại mật khẩu
const errorResetPass = ref('');





//  Đăng ký
const Handleregister = async () => {
  Object.keys(registerErrors).forEach(key => delete registerErrors[key]);
  loading.value = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', registerData);

    if (response.status === 200) {
      alert(response.data.message);

      // Lưu thông tin người dùng và token nếu backend trả về
      user.value = response.data.user;
      localStorage.setItem('user', JSON.stringify(response.data.user));
      localStorage.setItem('token', response.data.token);
      isLoggedIn.value = true;

      // Ẩn modal
      const modalElement = document.getElementById('registerModal');
      const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
      modalInstance.hide();

      // Xử lý backdrop thủ công nếu cần
      document.body.classList.remove('modal-open');
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

      // Reset form
      Object.keys(registerData).forEach(key => registerData[key] = '');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      if (error.response?.status === 422) {
        const allErrors = error.response.data.errors;
        const firstKey = Object.keys(allErrors)[0];

        // Xóa hết lỗi cũ
        Object.keys(registerErrors).forEach(k => delete registerErrors[k]);

        // Chỉ giữ lỗi đầu tiên
        registerErrors[firstKey] = allErrors[firstKey];
        firstErrorKey.value = firstKey;
      }
    } else {
      console.error('Lỗi khi đăng ký:', error);
      alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    }
  } finally {
    loading.value = false;
  }
};

//  Đăng nhập
const handleLogin = async () => {
  loginError.value = '';
  loading.value = true;

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', loginData);

    alert('Đăng nhập thành công!');
    user.value = response.data.user;
    localStorage.setItem('user', JSON.stringify(response.data.user));
    localStorage.setItem('token', response.data.token);
    isLoggedIn.value = true;

    // Ẩn modal
    const modalElement = document.getElementById('loginModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.hide();

    // Xử lý backdrop thủ công nếu cần
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());



    // Reset form
    loginData.login = '';
    loginData.password = '';
    if (user.value.role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/home');
    }
  } catch (error) {
    console.error('Lỗi đăng nhập:', error);

    if (error.response?.status === 422) {
      // Gộp tất cả lỗi từ backend thành 1 chuỗi
      const errors = error.response.data.errors;
      const firstKey = Object.keys(errors)[0];
      loginError.value = errors[firstKey][0];
    } else if (error.response?.status === 401) {
      loginError.value = 'Sai email hoặc mật khẩu!';
    } else if (error.response?.status === 500) {
      loginError.value = 'Lỗi máy chủ. Vui lòng thử lại sau.';
    } else if (error.request) {
      loginError.value = 'Không thể kết nối đến máy chủ. Kiểm tra internet.';
    } else {
      loginError.value = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
    }
  } finally {
    loading.value = false;
  }
};




//  Đăng xuất
const handleLogout = async () => {


  const confirmLogout = confirm('Bạn chắc chắn muốn đăng xuất?');
  if (!confirmLogout) {
    return;
  }
  try {
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    localStorage.removeItem('user');
    localStorage.removeItem('token');
    user.value = null;
    isLoggedIn.value = false;

    alert('Đăng xuất thành công');
    window.location.href = '/home';
  } catch (error) {
    console.error('Lỗi đăng xuất:', error);
    alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
  }
};


// hàm mở pop up Login
const openLoginModal = () => {
  // Xóa backdrop và class nếu bị sót từ lần trước
  document.body.classList.remove('modal-open');
  document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(el => el.remove());

  // Lấy modal element và hiển thị lại
  const modalElement = document.getElementById('loginModal');
  const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
  modalInstance.show();
};

// di chuyển ô input
const moveToNext = (index, event) => {
  const input = event.target
  const value = input.value

  if (value.length === 1 && index < 5) {
    const nextInput = input.nextElementSibling
    if (nextInput) nextInput.focus()
  } else if (value === '' && index > 0) {
    const prevInput = input.previousElementSibling
    if (prevInput) prevInput.focus()
  }
};

// chỉ nhập số
const onlyNumber = (event) => {
  const key = event.key
  if (!/^\d$/.test(key)) {
    event.preventDefault()
  }
};

const forgotPass = async () => {
  loading.value = true;
  errorSendCode.value = '';
  try {
    // Tính toán thời gian hết hạn từ lúc gửi request (5 phút từ thời điểm gửi)
    const expireTime = new Date().getTime() + 5 * 60 * 1000; // 5 phút (tính bằng ms)

    // Gửi request để yêu cầu mã xác nhận
    const response = await axios.post('http://127.0.0.1:8000/api/forgot', {
      email: verify.email
    });

    if (response.status === 200) {
      // alert(response.data.message);
      const modelElement = document.getElementById('forgotPasswordModal');
      const modalInstance = bootstrap.Modal.getInstance(modelElement) || new bootstrap.Modal(modelElement);
      modalInstance.hide();

      const modelCode = document.getElementById('authenticationModal');
      const modalInstanceCode = bootstrap.Modal.getInstance(modelCode) || new bootstrap.Modal(modelCode);
      modalInstanceCode.show();
    }



    // Gọi hàm startCountdown với expireTime tính từ lúc gửi request
    startCountdown(expireTime);

  } catch (error) {
    if (error.response) {
      const status = error.response.status;
      if (status === 404 || status === 410) {
        errorSendCode.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorSendCode.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorSendCode.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorSendCode.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loading.value = false;

  }
};





//hàm nhập code
const verifyResetCode = async () => {
  loading.value = true;

  const code = codeDigits.value.join('')
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/code', {
      'email': verify.email,
      'code': code
    })

    if (response.status == 200) {
      alert(response.data.message);
      const modalCode = document.getElementById('authenticationModal');
      const modalCodeInstance = bootstrap.Modal.getInstance(modalCode) || new bootstrap.Modal(modalCode);
      modalCodeInstance.hide();

      const modalResetPass = document.getElementById('resetModal');
      const modalResetPassInstance = bootstrap.Modal.getInstance(modalResetPass) || new bootstrap.Modal(modalResetPass);
      modalResetPassInstance.show();
      errorVerify.value = '';


    }
  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 404 || status === 410) {
        errorVerify.value = error.response.data.message;
      } else if (status === 422) {
        errorVerify.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorVerify.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorVerify.value = 'Lỗi kết nối đến máy chủ';
    }
  } finally {
    loading.value = false
  }
};


const sendCode = async () => {
  if (loadingSend.value || wait.value > 0) return;
  loadingSend.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/forgot', {
      email: verify.email
    });

    if (response.status === 200) {
      // alert(response.data.message);

    }

    wait.value = 60;
    const timer = setInterval(() => {
      if (wait.value > 0) wait.value--;
      else clearInterval(timer);
    }, 1000);


    const expireTime = new Date().getTime() + 5 * 60 * 1000;
    startCountdown(expireTime);

  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 404 || status === 410) {
        errorVerify.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorVerify.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorVerify.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorVerify.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loadingSend.value = false;
  }
};

const ResetPass = async () => {
  loading.value = true
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/reset-password', {
      "email": verify.email,
      "password": verify.password,
      "password_confirmation": verify.password_confirmation
    });

    if (response.status == 200) {
      alert(response.data.message);
      const modalResetPass = document.getElementById('resetModal');
      const modalResetPassInstance = bootstrap.Modal.getInstance(modalResetPass) || new bootstrap.Modal(modalResetPass);
      modalResetPassInstance.hide();
    }
  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 404 || status === 410) {
        errorResetPass.value = error.response.data.errors?.email?.[0] || error.response.data.message;
      } else if (status === 422) {
        errorResetPass.value = Object.values(error.response.data.errors)[0][0];
      } else {
        errorResetPass.value = 'Đã xảy ra lỗi không xác định';
      }
    } else {
      errorResetPass.value = 'Lỗi kết nối đến server';
    }
  } finally {
    loading.value = false;
  }

  return {

  }
};

// export {
//   registerData,
//   loginData,
//   loginError,
//   registerErrors,
//   loading,
//   user,
//   isLoggedIn,
//   errorSendCode,
//   errorVerify,
//   firstErrorKey,
//   codeDigits,
//   verify,
//   isComposing,
//   inputs,
//   errorResetPass,
//   Handleregister,
//   handleLogin,
//   handleLogout,
//   openLoginModal,
//   moveToNext,
//   onlyNumber,
//   forgotPass,
//   sendCode,
//   verifyResetCode,
//   ResetPass,
//   startCountdown,



// }const searchQuery = ref('')
const searchQuery = ref(''); // Từ khóa tìm kiếm
const suggestions = ref([]); // Danh sách kết quả
const offset = ref(0); // Vị trí bắt đầu
const limit = 5; // Số kết quả mỗi lần
const hasMore = ref(true); // Kiểm tra có còn dữ liệu để tải thêm không
const showSuggestions = ref(false); // Biến để điều khiển dropdown
const wrapperRef = ref(null); // Ref để gắn vào input-wrapper

// Hàm debounce để tránh gọi API quá nhanh
function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(this, args), delay);
  };
}

// Hàm xử lý khi người dùng nhập từ khóa tìm kiếm
const handleInput = debounce(() => {
  if (searchQuery.value.trim()) {
    offset.value = 0;
    suggestions.value = [];
    hasMore.value = true;
    showSuggestions.value = true;
    fetchSuggestions();
  } else {
    suggestions.value = [];
    showSuggestions.value = false;
  }
}, 300);
// 300ms debounce

// Hàm lấy dữ liệu từ API
const fetchSuggestions = async () => {
  if (loading.value || !searchQuery.value.trim() || !hasMore.value) return;

  loading.value = true;
  try {
    const res = await axios.get('http://localhost:8000/api/search', {
      params: {
        search: searchQuery.value,
        offset: offset.value,
        limit: limit,
      },
    });

    const results = res.data.results || [];
    const total = res.data.total || 0;

    console.log("Load thêm:", results.length, "offset:", offset.value, "total:", total);

    suggestions.value.push(...results);

    offset.value += results.length;
    hasMore.value = offset.value < total;
  } catch (error) {
    console.error('Lỗi khi fetch gợi ý:', error);
  } finally {
    loading.value = false;
  }
};



// Hàm xử lý cuộn để tải thêm dữ liệu
const handleScroll = (e) => {
  console.log("Đang scroll suggestion dropdown...");
  const el = e.target;
  if (
    el.scrollTop + el.clientHeight >= el.scrollHeight - 10 &&
    hasMore.value &&
    !loading.value
  ) {
    console.log("Gần cuối dropdown, tải thêm...");
    fetchSuggestions();
  }
};





// Hàm xử lý khi người dùng chọn một item trong danh sách gợi ý
const selectItem = (item) => {
  console.log("Selected item:", item);
  searchQuery.value = item.name;
  suggestions.value = [];
  showSuggestions.value = false; // Ẩn dropdown khi chọn item
};

// Hàm tìm kiếm sản phẩm khi người dùng nhấn Enter hoặc submit
const searchProduct = () => {
  if (searchQuery.value.trim()) {
    showSuggestions.value = false;
    router.push({
      path: '/search', // đường dẫn của route
      query: { search: searchQuery.value }
    });
  }
};

// Hàm xử lý khi người dùng click ngoài để ẩn dropdown
const handleClickOutside = (e) => {
  if (wrapperRef.value && !wrapperRef.value.contains(e.target)) {
    showSuggestions.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>


<style scoped>
.text-primary-red {
  color: #ca111f;
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.1);
}

.suggestion-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  max-height: 270px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #ddd;
  z-index: 999;
  list-style: none;
  margin: 0;
  padding: 0;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.suggestion-dropdown li {
  padding: 17px 16px;
  font-size: 16px;
  cursor: pointer;
}

.suggestion-dropdown li:hover {
  background-color: #f6f6f6;
}

.loading,
.no-more {
  padding: 10px;
  text-align: center;
  color: #888;
}

.loader {
  width: fit-content;
  height: fit-content;
  display: flex;
  align-items: center;
  justify-content: center;
}

.truckWrapper {
  margin-right: 16px;
  width: 38px;
  height: 22px;
  display: flex;
  flex-direction: column;
  position: relative;
  align-items: center;
  justify-content: flex-end;
  overflow-x: hidden;
}

/* Truck body */
.truckBody {
  width: 26px;
  height: fit-content;
  margin-bottom: 1px;
  animation: motion 1s linear infinite;
}

/* Suspension animation */
@keyframes motion {
  0% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(0.5px);
  }

  100% {
    transform: translateY(0px);
  }
}

/* Tires */
.truckTires {
  width: 26px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0px 2px 0px 3px;
  position: absolute;
  bottom: 0;
}

.truckTires svg {
  width: 5px;
  height: 5px;
}

/* Road */
.road {
  width: 100%;
  height: 1px;
  background-color: #282828;
  position: relative;
  bottom: 0;
  align-self: flex-end;
  border-radius: 1px;
}

.road::before {
  content: "";
  position: absolute;
  width: 4px;
  height: 100%;
  background-color: #282828;
  right: -50%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 1px solid white;
}

.road::after {
  content: "";
  position: absolute;
  width: 2px;
  height: 100%;
  background-color: #282828;
  right: -65%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 0.5px solid white;
}

/* Lamp post */
.lampPost {
  position: absolute;
  bottom: 0;
  right: -90%;
  height: 18px;
  animation: roadAnimation 1.4s linear infinite;
}

@keyframes roadAnimation {
  0% {
    transform: translateX(0px);
  }

  100% {
    transform: translateX(-90px);
  }
}
</style>
