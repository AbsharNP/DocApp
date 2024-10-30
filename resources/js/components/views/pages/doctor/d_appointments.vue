<template>
  <div class="container-fluid vh-100 d-flex flex-column">
    <div class="row flex-grow-1">
      <div class="col-12 d-flex flex-column align-items-center">
        <div class="container py-4">
          <h3>Your Appointments</h3>

          <!-- Buttons for filtering appointments -->
          <div class="mb-3 d-flex justify-content-center">
            <button 
              class="btn btn-primary rounded-btn mx-2" 
              :class="{ active: showToday }" 
              @click="fetchTodayAppointments"
            >
              Today's Appointments
            </button>
            <button 
              class="btn btn-secondary rounded-btn mx-2" 
              :class="{ active: showAll }" 
              @click="fetchAllAppointments"
            >
              All Appointments
            </button>
          </div>

          <!-- Flexible container for the table -->
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Session Date</th>
                  <th v-if="showToday">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="appointments.length === 0">
                  <td colspan="3" class="text-center">No appointments found.</td> 
                </tr>
                <tr v-for="appointment in appointments.slice(0, 5)" :key="appointment.id">
                  <td>{{ appointment.name }}</td>
                  <td>{{ appointment.session_date }}</td> 
                  <td v-if="showToday">
                    <router-link 
                      :to="`/treatment/${appointment.id}`" 
                      :class="[ 
                        'btn', 
                        appointment.treatment_status === 1 ? 'btn-outline-secondary' : 'btn-success',
                        'rounded-btn', 
                        'd-flex', 
                        'align-items-center' 
                      ]"
                    >
                      Go
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L9.293 7.5H2.5a.5.5 0 0 0 0 1h6.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                      </svg>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      appointments: [],
      doctorId: localStorage.getItem('doctorId'),
      showToday: true,
      showAll: false,
    };
  },
  mounted() {
    this.fetchTodayAppointments();  
  },
  methods: {
    fetchTodayAppointments() {
      this.showToday = true;
      this.showAll = false;

      // Fetch only today's appointments
      axios.post(`/appointments/today/${this.doctorId}`)
        .then(response => {
          this.appointments = response.data.map(appointment => ({
            ...appointment,
            session_date: this.formatDate(appointment.session_date)
          }));
        })
        .catch(error => {
          console.error("Error fetching today's appointments:", error);
        });
    },

    fetchAllAppointments() {
      this.showToday = false;
      this.showAll = true;

      // Fetch all appointments
      axios.post(`/appointments/${this.doctorId}`)
        .then(response => {
          this.appointments = response.data
            .map(appointment => ({
              ...appointment,
              session_date: this.formatDate(appointment.session_date)
            }))
            .sort((a, b) => new Date(a.session_date) - new Date(b.session_date));
        })
        .catch(error => {
          console.error("Error fetching all appointments:", error);
        });
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) {
        return 'Invalid Date';  // Handle invalid date
      }
      return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: '2-digit' });
    },
  },
};
</script>

<style scoped>
.container-fluid {
  height: 100vh;
}

.table-responsive {
  display: flex;
  justify-content: center;
  width: 100%;
  overflow-x: auto;
}

.table {
  margin-top: 1rem;
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #dee2e6;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f8f9fa;
}

.table tbody tr:hover {
  background-color: #e9ecef;
  cursor: pointer;
}

.rounded-btn {
  border-radius: 30px;
}

.active {
  background-color: #28a745 !important;
  color: white;
}

@media (max-width: 768px) {
  .table th, .table td {
    padding: 10px;
    font-size: 0.9rem;
  }
}
</style>
