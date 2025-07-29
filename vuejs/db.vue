<template>
    <body>
    <div class="booking-section-wrapper">
    <div class="booking-section">
      <h2>Find Your Perfect Dining Experience</h2>
      <p class="subtitle">Book a table for any occasion, from a casual lunch to a special celebration. Discover top restaurants, cafes, and bars near you.</p>

      <div class="booking-form">
        <div class="form-group">
          <i class="icon fas fa-calendar-alt"></i>
          <input type="date" />
        </div>
        <div class="form-group">
          <i class="icon fas fa-clock"></i>
          <select>
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
          <select>
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
          <input type="text" placeholder="Location, Restaurant, or Cuisine" />
        </div>
        <button class="btn-primary">Let's go</button>
      </div>

      <div class="location-info">
        It looks like you're in Ho Chi Minh City. Not correct?
        <a href="#"><i class="icon fas fa-location-crosshairs"></i> Get current location</a>
      </div>

      <div class="featured-categories">
        <h3>Explore Popular Cuisines & Occasions</h3>
        <div class="category-grid">
          <div class="category-item"><i class="icon fas fa-pizza-slice"></i><p>Italian</p></div>
          <div class="category-item"><i class="icon fas fa-fish"></i><p>Seafood</p></div>
          <div class="category-item"><i class="icon fas fa-hotdog"></i><p>Fast Food</p></div>
          <div class="category-item"><i class="icon fas fa-utensils"></i><p>Vietnamese</p></div>
          <div class="category-item"><i class="icon fas fa-leaf"></i><p>Vegetarian</p></div>
          <div class="category-item"><i class="icon fas fa-birthday-cake"></i><p>Birthday</p></div>
          <div class="category-item"><i class="icon fas fa-heart"></i><p>Romantic</p></div>
          <div class="category-item"><i class="icon fas fa-briefcase"></i><p>Business Lunch</p></div>
        </div>
      </div>
    </div>
  </div>
  </body>
</template>

<style>
:root {
  --primary-red: #da3743;
  --light-gray: #e0e0e0;
  --medium-gray: #a0a0a0;
  --dark-gray: #333;
  --white: #fff;
  --border-color: #ccc;
  --shadow-color: rgba(0, 0, 0, 0.1);
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  background-color: #f8f8f8;
}
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








.booking-section-wrapper {
  max-width: 1200px;
  padding: 20px;
  margin: 0 auto;
}

.booking-section {
  background-color: var(--white);
  border-radius: 12px;
  box-shadow: 0 8px 25px var(--shadow-color);
  padding: 30px;
  text-align: center;
}

.booking-section h2 {
  font-size: 2.2em;
  color: var(--dark-gray);
  margin-bottom: 25px;
  font-weight: 700;
}

.booking-section p.subtitle {
  font-size: 1.1em;
  color: var(--medium-gray);
  margin-bottom: 40px;
  line-height: 1.6;
}



.location-info {
  margin-top: 25px;
  font-size: 0.95em;
  color: var(--medium-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px;
}

.location-info .icon {
  color: var(--primary-red);
  font-size: 1.1em;
}

.location-info a {
  color: var(--primary-red);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.location-info a:hover {
  text-decoration: underline;
  color: #c02d38;
}

.featured-categories {
  margin-top: 50px;
  padding-top: 40px;
  border-top: 1px solid var(--light-gray);
}

.featured-categories h3 {
  font-size: 1.8em;
  color: var(--dark-gray);
  margin-bottom: 25px;
  font-weight: 700;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
  justify-content: center;
}

.category-item {
  background-color: var(--white);
  border: 1px solid var(--light-gray);
  border-radius: 10px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 120px;
  cursor: pointer;
}

.category-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px var(--shadow-color);
}

.category-item .icon {
  font-size: 2.5em;
  color: var(--primary-red);
  margin-bottom: 10px;
}

.category-item p {
  font-size: 1em;
  color: var(--dark-gray);
  font-weight: 500;
  margin: 0;
}
</style>