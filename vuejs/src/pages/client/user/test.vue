<template>
    <div class="container py-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 600px;">
      <h4 class="text-center fw-bold mb-4">MANAGE ACCOUNT</h4>
      <div class="mb-3">
        <label class="form-label">Nickname</label>
        <input type="text" class="form-control form-control-lg" placeholder="Your nickname" value="minhqua30032005">
      </div>
      <div class="mb-4 text-center">
        <label class="form-label d-block">Profile Picture</label>
        <div class="border border-2 rounded-3 d-inline-block p-4" style="cursor: pointer;">
          <input type="file" class="d-none" id="upload-profile">
          <label for="upload-profile" class="text-muted">
            <div class="fs-1 mb-2">+</div>
            <div class="fw-medium">Upload</div>
          </label>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Mobile number <span class="text-warning">+5Points</span></label>
        <div class="input-group">
          <span class="input-group-text">+84</span>
          <input type="text" class="form-control" placeholder="Enter your mobile phone number">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Birthday <span class="text-warning">+5Points</span></label>
        <input type="date" class="form-control">
      </div>
      <div class="mb-4">
        <label class="form-label">Gender <span class="text-warning">+5Points</span></label>
        <select class="form-select">
          <option value="">Choose</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="text-center">
        <button style="background-color: black;" class="btn text-white w-100">SAVE PROFILE</button>
      </div>
    </div>
  </div>
  
</template>