<template>
  <div class="container-fluid vh-100 d-flex flex-column">
    <!-- Header Section -->
    <div class="card p-4 mb-4 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h4">Welcome!</h1>
          <img 
            v-if="profilePic" 
            alt="avatar" 
            :src="profilePic" 
            class="rounded small-avatar" 
          />
          <h2 class="h2"><span class="un">{{ userName }}</span></h2>
          <p class="text-muted">
            Haven't any idea about doctors? No problem, let's jump to the 
            <strong>
              "<a href="/doc-list" class="doctor-link">All Doctors</a>"
            </strong>.<br />
            Track your past and future appointments history.
            Also find out the expected arrival time of your doctor or medical consultant.
          </p>
        </div>
        <div class="text-center date-container" style="width: 200px;">
          <p class="mb-0 text-muted">Today's Date</p>
          <h3 class="date-display">{{ todayDate }}</h3>
        </div>
      </div>
    </div>

    <!-- Appointments Section -->
    <div>
      <h2 class="my-4">My Appointments</h2>

      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div class="row">
        <div class="col-md-4 col-lg-3 mb-4" v-for="appointment in appointments" :key="appointment.id">
          <div class="card appointment-card">
            <div class="card-body">
              <h5 class="card-title">{{ capitalizeWords(appointment.name) }}</h5>
              <p class="card-text">
                <strong>Date:</strong> {{ formatDate(appointment.appsession.session_date) }}<br />
                <strong>Doctor:</strong> {{ capitalizeWords(appointment.dr_name) }}<br />
                <strong>Status:</strong> {{ appointment.treatment_status ? 'Completed' : 'Scheduled' }}
              </p>
              <div class="d-flex justify-content-between">
                <router-link 
                  v-if="appointment.treatment_status === 1" 
                  :to="`/view-prescriptions`" 
                  class="btn btn-outline-info"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                  stroke-width="2" stroke-linecap="round" 
                  stroke-linejoin="round" class="feather feather-eye">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </router-link>
                <button 
                  v-else 
                  class="btn btn-outline-danger" 
                  @click="deleteAppointment(appointment.id)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
                  viewBox="0 0 24 24" fill="none" 
                  stroke="currentColor" stroke-width="2" 
                  stroke-linecap="round" stroke-linejoin="round" 
                  class="feather feather-trash">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cards Section -->
    <div class="row mb-3 flex-fill justify-content-center">
      <div class="col-md-4 col-lg-3 d-flex align-items-stretch mb-3" v-for="card in cards" :key="card.title">
        <router-link :to="card.link" class="text-decoration-none flex-fill">
          <div class="card text-center p-3 shadow-sm custom-card capsule-card">
            <h4 class="display-6 card-title">{{ card.title }}</h4>
            <p class="card-description">{{ card.description }}</p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      todayDate: new Date().toISOString().split('T')[0],
      userName: localStorage.getItem('userName'),
      profilePic: '',
      cards: [
        { title: 'Get Appointment', description: 'Book your appointment now', link: '/doc-list' },
        // { title: 'My Appointments', description: 'Track all your current appointments', link: '/my-appointment' },
        // { title: 'Prescriptions', description: 'Check your prescribed medications', link: '/view-prescriptions' },
      ],
      appointments: [],
      error: null,
    };
  },
  mounted() {
    this.fetchAppointments();
    this.getProfilePic(); // Call the method to fetch the profile picture
  },
  methods: {
    async fetchAppointments() {
      try {
        const userId = localStorage.getItem('userId'); // Get user ID from local storage
        const response = await axios.post('/my-appointments', { user_id: userId }); // Send user ID in request body
        this.appointments = response.data.sort((a, b) => {
          const dateA = new Date(a.appsession.session_date);
          const dateB = new Date(b.appsession.session_date);
          return dateA - dateB; // Sort in ascending order (oldest first) 
        });
      } catch (error) {
        console.error('Error fetching appointments:', error.response?.data || error);
        this.error = 'Failed to load appointments.';
      }
    },

    async getProfilePic() {
      const userId = localStorage.getItem('userId'); // Get user ID from local storage
      try {
        const response = await axios.post(`/getProfilePic/${userId}`);
        this.profilePic = response.data.profile_pic || '/images/default-avatar.jpg'; // Fallback to default avatar if no picture
      } catch (error) {
        console.error('Error fetching profile picture:', error);
        this.profilePic = '/images/default-avatar.jpg'; // Fallback to default avatar on error
      }
    },

    async deleteAppointment(id) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/appointments/${id}`);
          this.appointments = this.appointments.filter(appointment => appointment.id !== id);
          Swal.fire('Deleted!', 'Your appointment has been deleted.', 'success');
        } catch (error) {
          console.error('Error deleting appointment:', error.response?.data || error);
          this.error = 'Failed to delete appointment.';
          Swal.fire('Error!', 'There was a problem deleting the appointment.', 'error');
        }
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    capitalizeWords(str) {
      return str.split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
      ).join(' ');
    },
  },
};
</script>

<style scoped>
.container-fluid {
  padding: 20px; /* Add padding for better spacing */
}

.custom-card {
  background-color: #506690; /* Custom background color */
  color: white; /* White text color for contrast */
  transition: transform 0.3s, box-shadow 0.3s; /* Smooth transition for hover effects */
}

.capsule-card {
  border-radius: 50px; /* Increase border radius for a capsule effect */
  padding: 20px; /* Add padding for better spacing inside the card */
}

.custom-card:hover {
  transform: scale(1.05); /* Slightly increase size on hover */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Add shadow effect on hover */
}

.card-title {
  font-size: 1.25rem; /* Adjust font size for card title */
}

.card-description {
  font-size: 0.875rem; /* Adjust font size for card description */
}

.un {
  font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
  color: #515365;
}

.doctor-link {
  color: #515365; 
  font-weight: bold; 
  transition: color 0.3s ease; 
}

.doctor-link:hover {
  color: #1d08fb; /* Darker shade on hover */
}

.table {
  margin-top: 20px; /* You can adjust this value for more/less space */
  width: 100%;
}

.table th,
.table td {
  text-align: center;
}

.alert {
  margin-top: 20px;
}

.vh-100 {
  height: 100vh;
}

.btn-danger {
  margin-left: 10px;
}
.gg {
  margin-bottom: 40px;
}
.small-avatar {
  width: 100px; 
  height: 100px;
  object-fit: cover; 
  border-radius: 50%; 
  /* border: 2px solid #dee2e6;  */
  box-shadow: 2px;
}
</style>
