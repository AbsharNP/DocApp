<template>
  <div class="container-fluid vh-100 d-flex flex-column">
    <div class="row flex-grow-1">
      <div class="col-12">
        <!-- Main Content -->
        <div class="container">
          <!-- Header Section -->
          <div class="card p-4 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h4">Welcome!</h1>
                <h2 class="h2">Dr. {{ capitalizedUserName }}</h2>
                <p class="text-muted">
                  Thanks for joining with us. We are always trying to get you a complete service.
                  You can view your daily schedule, Reach Patients Appointment at home!
                </p>
                <router-link to="/appointment">
                  <button class="btn btn-outline-success">
                    My Appointments
                  </button>
                </router-link>
              </div>
              <div class="text-center date-container" style="width: 200px;">
                <p class="mb-0 text-muted">Today's Date</p>
                <h3 class="date-display">{{ todayDate }}</h3>
              </div>
            </div>
          </div>
          <div class="card p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h3>Upcoming Sessions</h3>
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                Add New Session
              </button>
            </div>
            <table class="table table-hover mt-3">
              <thead class="table-light">
                <tr>
                  <th scope="col">SI No.</th>
                  <th scope="col">Scheduled Date</th>
                  <th scope="col">Start Time</th>
                  <th scope="col">End Time</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="sessions.length === 0">
                  <td colspan="4" class="text-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                      <p class="mt-3 text-muted">We couldn't find anything related to your schedule</p>
                    </div>
                  </td>
                </tr>
                <tr v-else v-for="(session, index) in sessions" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ formatDate(session.session_date) }}</td>
                  <td>{{ session.start_time }}</td>
                  <td>{{ session.end_time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for creating a session -->
    <div class="modal fade" id="createSessionModal" tabindex="-1" aria-labelledby="createSessionLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createSessionLabel">Add New Session</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createSession">
              <div class="mb-3">
                <label for="sessionDate" class="form-label">Session Date</label>
                <input type="date" class="form-control" id="sessionDate" v-model="form.session_date" required>
              </div>
              <div class="mb-3">
                <label for="startTime" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="startTime" v-model="form.start_time" @change="calculateDuration" required>
              </div>
              <div class="mb-3">
                <label for="endTime" class="form-label">End Time</label>
                <input type="time" class="form-control" id="endTime" v-model="form.end_time" @change="calculateDuration" required>
              </div>
              <div class="mb-3">
                <label for="numberOfSessions" class="form-label">Number of Sessions</label>
                <input type="number" class="form-control" id="numberOfSessions" v-model="form.no_of_sessions" readonly>
              </div>
              <button type="submit" class="btn btn-primary">Create Session</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      todayDate: new Date().toISOString().split('T')[0],
      sessions: [],
      userName: localStorage.getItem('userName'),
      form: {
        session_date: '',
        start_time: '',
        end_time: '',
        doctor_id: '',
        no_of_sessions: 0,
      },
      duration: 0,
    };
  },
  computed: {
    capitalizedUserName() {
      return this.userName ? this.userName.charAt(0).toUpperCase() + this.userName.slice(1) : 'User';
    },
  },
  mounted() {
    const doctorId = localStorage.getItem('doctorId');
    const dr_name = localStorage.getItem('userName');

    if (doctorId && dr_name) {
      this.form.doctor_id = doctorId;
      this.form.dr_name = dr_name;
      this.fetchUpcomingSessions();
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Doctor ID not found. Please log in again.',
      });
      this.$router.push('/login');
    }
  },
  methods: {
    fetchUpcomingSessions() {
      axios.post(`/upcsessions/${this.form.doctor_id}`)
        .then((response) => {
          this.sessions = response.data;
        })
        .catch((error) => {
          console.error("Error fetching upcoming sessions:", error);
        });
    },
    calculateDuration() {
      const start = moment(this.form.start_time, 'HH:mm');
      const end = moment(this.form.end_time, 'HH:mm');

      if (!start.isValid() || !end.isValid()) {
        this.duration = 0;
        this.form.no_of_sessions = 0;
        return;
      }

      let diffInHours = end.diff(start, 'hours');
      if (diffInHours < 0) {
        end.add(1, 'day');
        diffInHours = end.diff(start, 'hours');
      }

      this.duration = diffInHours;
      this.form.no_of_sessions = this.duration * 3; // Assuming each session is 20 minutes
    },
    createSession() {
  axios.post('/create-sessions', this.form)
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Session created successfully',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
      });
      this.resetForm();
      // Close the modal after a successful session creation
      const modal = new bootstrap.Modal(document.getElementById('createSessionModal'));
      modal.hide();
      // Refresh the page
      window.location.reload();
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Creation failed',
        text: error.response.data.message || 'An error occurred. Please try again.',
      });
    });
},
    resetForm() {
      this.form.session_date = '';
      this.form.start_time = '';
      this.form.end_time = '';
      this.form.no_of_sessions = 0;
      this.duration = 0;
    },
    formatDate(date) {
      return moment(date).format('MMMM Do YYYY'); // Adjust format as needed
    },
  },
};
</script>

<style scoped>
.vh-100 {
  height: 100vh;
}

.card {
  border-radius: 10px;
}

.table tbody tr:hover {
  cursor: pointer;
  background-color: #f8f9fa;
}

.date-container {
  text-align: center;
}

.date-display {
  margin-top: 0.2rem;
  font-size: 1.75rem;
}


@media (max-width: 768px) {
  .date-display {
    font-size: 1.5rem;
  }
}
</style>
