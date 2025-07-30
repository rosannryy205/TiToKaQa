<!-- components/ReservationForm.vue -->
<template>
    <div class="booking-form">
      <div class="form-group">
        <i class="icon fas fa-calendar-alt"></i>
        <input type="date" v-model="reservation.date" />
      </div>
  
      <div class="form-group">
        <i class="icon fas fa-clock"></i>
        <select v-model="reservation.time">
          <option>5:00 PM</option>
          <option>5:30 PM</option>
          <option>6:00 PM</option>
          <option>6:30 PM</option>
          <option>7:00 PM</option>
          <option>7:30 PM</option>
          <option>8:00 PM</option>
          <option>8:30 PM</option>
          <option>9:00 PM</option>
        </select>
      </div>
  
      <div class="form-group">
        <i class="icon fas fa-users"></i>
        <select v-model="reservation.guests">
          <option>1 person</option>
          <option>2 persons</option>
          <option>3 persons</option>
          <option>4 persons</option>
          <option>5 persons</option>
          <option>6 persons</option>
          <option>7 persons</option>
          <option>8 persons</option>
          <option>9 persons</option>
          <option>10+ people</option>
        </select>
      </div>
  
      <div class="form-group search-input">
        <i class="icon fas fa-search"></i>
        <input type="text" v-model="reservation.query" placeholder="Location, Restaurant, or Cuisine" />
      </div>
  
      <button class="btn-primary" @click="submitReservation">Let's go</button>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue'
  
  const reservation = reactive({
    date: '',
    time: '',
    guests: '',
    query: ''
  })
  
  const submitReservation = () => {
    if (!reservation.date || !reservation.time || !reservation.guests) {
      alert('Vui lòng nhập đầy đủ thông tin đặt bàn!')
      return
    }
  
    console.log('Thông tin đặt bàn:', reservation)
    alert('Đặt bàn thành công!')
  }
  </script>
  
  <style scoped>
  .booking-form {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
  }
  
  .form-group {
    position: relative;
    flex: 1 1 auto;
    min-width: 180px;
    max-width: 250px;
  }
  
  .form-group input,
  .form-group select {
    width: 100%;
    padding: 14px 20px 14px 45px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1em;
    color: var(--dark-gray);
    background-color: var(--white);
    transition: all 0.3s ease;
  }
  
  .form-group .icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--medium-gray);
    pointer-events: none;
    font-size: 1.1em;
  }
  
  .search-input {
    flex: 2 1 auto;
    min-width: 280px;
    max-width: 450px;
  }
  
  .btn-primary {
    background-color: var(--primary-red);
    color: var(--white);
    padding: 14px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    flex-shrink: 0;
  }
  
  .btn-primary:hover {
    background-color: #c02d38;
    transform: translateY(-2px);
  }
  </style>
  