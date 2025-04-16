<template>
    <div class="container-fluid py-5">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-4 col-lg-3">
      <div class="card shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center gap-3">
            <img src="https://via.placeholder.com/60" alt="avatar" class="rounded-circle" width="60" height="60">
            <div>
              <h6 class="mb-0 fw-bold">minhqua30032005</h6>
              <div class="small"><a href="#" class="link-primary text-decoration-none">Edit Profile</a> · <a href="#" class="link-secondary text-decoration-none">Account Settings</a></div>
            </div>
          </div>
          <a href="#" class="text-danger small">Sign out</a>
        </div>

        <div class="bg-light rounded-3 p-3 text-center mb-3">
          <div class="fw-bold">POP MART MEMBER</div>
          <div class="d-flex justify-content-around mt-2">
            <div>
              <div class="fw-bold fs-5">50</div>
              <div class="text-muted small">Points</div>
            </div>
            <div>
              <div class="fw-bold fs-5">0</div>
              <div class="text-muted small">Coupons</div>
            </div>
          </div>
          <button class="btn btn-outline-dark btn-sm mt-3 rounded-pill px-4">Rewards</button>
        </div>

        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-bold text-danger">My Orders</div>
              <div class="small text-muted">Manage your order</div>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-bold">Address Book</div>
              <div class="small text-muted">Manage your order address</div>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-bold">Earned Rewards</div>
              <div class="small text-muted">Manage your earned rewards</div>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-8 col-lg-9">
      <h4 class="fw-bold mb-4">My Orders</h4>
      <div class="text-center mt-5">
        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
          <i class="bi bi-receipt fs-2 text-muted"></i>
        </div>
        <p class="text-muted mt-3">You don't have any orders yet.</p>
      </div>
    </div>
  </div>
</div>

</tempalte>