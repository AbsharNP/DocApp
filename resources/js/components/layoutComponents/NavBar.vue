<template>
  <div>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
      <div class="container">
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarResponsive"
          aria-controls="navbarResponsive"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav me-auto">
            <li class="nav-item" v-if="userType === 'admin'">
              <a class="nav-link" href="/admin"><i class="bi bi-house-door-fill"></i>Home</a>
            </li>
            <li class="nav-item" v-if="userType === 'patient'">
              <a class="nav-link" href="/patient-dash"><i class="bi bi-house-door-fill"></i>Home</a>
            </li>
            <li class="nav-item" v-if="userType === 'doctor'">
              <a class="nav-link" href="/doctor-dash"><i class="bi bi-house-door-fill"></i>Home</a>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <div v-if="userType === 'doctor'">
              <li class="nav-item">
                <a class="nav-link" href="/appointment">Appointments</a>
              </li>
            </div>
            <div v-if="userType === 'patient'">
              <li class="nav-item">
                <a class="nav-link" href="/view-prescriptions">History</a>
              </li>
            </div>
            <div v-if="userType === 'patient'">
              <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="/profile">
                  <img
                    
                    alt="avatar"
                    :src="profilePic"
                    class="small-avatar"
                  />

                  
                </a>
              </li>
            </div>
            <li class="nav-item">
              <a class="nav-link" href="#" @click="logout">
                <i class="bi bi-box-arrow-right"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'NavBar',
  data() {
    return {
      userType: null,
      profilePic: null, // Initialize profilePic to null
      userId: localStorage.getItem('userId'), // Get user ID from localStorage
    };
  },
  mounted() {
    // Get userType from localStorage when the component is mounted
    this.userType = localStorage.getItem('userType');
    this.getProfilePic(); // Call function to get profile picture
  },
  methods: {
    getProfilePic() {
      axios
        .post(`/getProfilePic/${this.userId}`)
        .then((response) => {
          this.profilePic = response.data.profile_pic;
        })
        .catch((error) => {
          console.error('Error fetching profile picture:', error);
          this.profilePic = '/images/default-avatar.jpg'; // Fallback to default avatar
        });
    },
    // Function to handle user logout
    async logout() {
      try {
        const response = await axios.post('/logout');

        if (response.status === 200) {
          // Clear all local storage items
          localStorage.removeItem('authToken');
          localStorage.removeItem('userName');
          localStorage.removeItem('userEmail');
          localStorage.removeItem('isAuthenticated');
          localStorage.removeItem('userId');
          localStorage.removeItem('userType');
          localStorage.removeItem('profile_pic');
          localStorage.removeItem('doctorId');

          this.$router.push({ name: 'login' });
          this.$toast.success('Logged out successfully');
        } else {
          this.$toast.error('Error logging out. Please try again.');
        }
      } catch (error) {
        console.error('Error logging out:', error);
        this.$toast.error('Error logging out');
      }
    },
  },
};
</script>

<style scoped>
.navbar {
  background-color: #f8f9fa; /* Light background color */
  border-bottom: 1px solid #dee2e6; /* Bottom border for navbar */
}

.navbar-brand {
  font-weight: bold; /* Make the brand name bold */
}

.nav-item {
  margin-left: 15px; /* Space between nav items */
}

.nav-item a {
  color: #495057; /* Text color for links */
  transition: color 0.3s; /* Smooth transition for hover effect */
}

.nav-item a:hover {
  color: #007bff; /* Change color on hover */
}

.nav-item.active a {
  color: #007bff; /* Active link color */
  font-weight: bold; /* Make active link bold */
}

.shadow-sm {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; /* Slight shadow for depth */
}

.small-avatar {
  width: 40px; /* Set width of the image */
  height: 40px; /* Set height of the image */
  object-fit: cover; /* Ensures the image fits the box while maintaining aspect ratio */
  border-radius: 50%; /* Makes the image circular */
  border: 2px solid #dee2e6; /* Optional: add a light border around the image */
}
</style>
