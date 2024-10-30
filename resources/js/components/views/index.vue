<template>
  <div>
    <!-- Navbar with Sign In and Sign Up buttons -->
    <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
      <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link class="btn btn-outline-light ms-2 blurred-button" to="/login">Sign In</router-link>
            </li>
            <li class="nav-item">
              <router-link class="btn btn-outline-light ms-2 blurred-button" to="/signup">Sign Up</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main content with background image -->
    <div class="main-content d-flex">
      <div class="background-image"></div>
      <div class="content-container">
        <div class="content">
          <h1 class="title">Avoid Hassles & Delays</h1>
          <p class="description">
            How is health today? Sounds like not good!<br >
            Don't worry. Find your doctor online, book as you wish with DocApp<br>
            We offer you a free doctor channeling service. Make your appointment now!
          </p>
          <router-link to="/login" class="btn btn-outline-light ms-1 blurred-button">Make Appointment</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Index',
};
</script>

<style scoped>
.navbar {
  position: absolute;
  width: 100%;
  z-index: 100;
  padding: 10px 20px; /* Padding for better spacing */
}

.main-content {
  display: flex;
  height: 100vh; /* Full height */
  width: 100%;
  overflow: hidden; /* Prevent overflow of content */
}

.background-image {
  background-image: url(/images/bgindex.jpg);
  background-size: cover;
  background-position: left; /* Align the image to the left */
  width: 50%; /* Take half of the width for portrait layout */
  filter: brightness(70%); /* Darken the background image for contrast */
}

.content-container {
  width: 50%; /* Take the other half */
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #515365; /* Semi-transparent dark background */
  padding: 40px; /* Increased padding around content */
}

.content {
  color: white;
  max-width: 600px; /* Limit width for better readability */
  text-align: left; /* Align text to the left for a cleaner look */
}

/* Title styling */
.title {
  font-size: 2.8rem; /* Larger font size for emphasis */
  margin-bottom: 20px; /* Space below title */
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8); /* Enhanced shadow for better readability */
}

/* Paragraph styling */
.description {
  font-size: 1.4rem; /* Slightly larger font for readability */
  line-height: 1.6; /* Increased line height for better spacing */
  margin-bottom: 30px; /* Space below paragraph */
}

/* Blur effect for buttons */
.blurred-button {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.1); /* Slight white transparent background */
  border-radius: 10px;
  padding: 0.75rem 1.5rem; /* Increased padding for a more button-like feel */
  color: white !important;
  transition: background 0.3s ease, transform 0.3s ease; /* Added transition for transform */
}

.blurred-button:hover {
  background: rgba(255, 255, 255, 0.2); /* Slightly more opaque on hover */
  transform: scale(1.05); /* Slightly enlarge button on hover */
}

/* Style adjustments for Sign In and Sign Up buttons */
.nav-link {
  font-weight: bold;
}

.btn-outline-light {
  color: white;
  border-color: white;
  font-weight: bold; /* Bold text for buttons */
  transition: color 0.3s ease; /* Added transition for color */
}

.btn-outline-light:hover {
  color: #fff;
  background-color: rgba(0, 123, 255, 0.7); /* More pronounced background on hover */
  border-color: rgba(0, 123, 255, 0.7); /* Match border with background on hover */
}
</style>
