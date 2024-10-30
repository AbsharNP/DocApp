<template>
  <div class="container-fluid d-flex flex-column">
    <h2 class="text-center my-4">Appointments</h2>

    <!-- Description Container -->
    <div class="description-container text-center mb-4">
      <p class="lead">
        View your prescription history and manage your health effectively. Click on the "View Prescription" button to see details of your treatments.
      </p>
    </div>

    <div class="appointment-list d-flex flex-wrap justify-content-start">
      <div class="card m-3" v-for="appointment in appointments" :key="appointment.id" style="width: 20rem;">
        <div class="card-body">
          <h5 class="card-title">{{ appointment.name }}</h5>
          <h6 class="card-title">Dr: {{ appointment.dr_name }}</h6>
          <h6 class="card-subtitle mb-3 text-muted">Updated: {{ formatDate(appointment.updated_at) }}</h6>
          <button class="btn btn-primary" @click="showPrescription(appointment)">View Prescription</button>
        </div>
      </div>
    </div>

    <div v-if="showPopup" class="popup-overlay" @click="handleOverlayClick">
      <div class="popup-container" @click.stop>
        <div class="prescription-container">
          <div class="header">
            <h1>PRESCRIPTION</h1>
            <p>Treatment Facility</p>
            <div class="facility-info">
              <div>
                <p><strong>Address:</strong> {{ facilityAddress }}</p>
                <p><strong>Phone:</strong> {{ facilityPhone }}</p>
              </div>
              <div>
                <p><strong>Attended Physician:</strong> Dr. {{ selectedAppointment.dr_name }}</p>
              </div>
            </div>
          </div>
          <hr>
          <div class="patient-info">
            <h4>Patient Info:</h4>
            <p><strong>Name:</strong> {{ selectedAppointment.name }}</p>
            <p><strong>Dr:</strong> {{ selectedAppointment.dr_name }}</p>
            <p><strong>Updated Date:</strong> {{ formatDate(selectedAppointment.updated_at) }}</p>
          </div>
          <hr>
          <div class="rx-section">
            <h5>Prescription:</h5>
            <p>{{ selectedAppointment.prescription }}</p>
          </div>
          <!-- Remove the download PDF button -->
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
      selectedAppointment: null,
      showPopup: false,
      facilityAddress: '123 Treatment Ave',
      facilityPhone: '123-456-7890',
      userId: localStorage.getItem('userId'),
    };
  },
  mounted() {
    this.fetchAppointments();
  },
  methods: {
    fetchAppointments() {
      axios.post(`/getprescription?user_id=${this.userId}`)
        .then(response => {
          this.appointments = response.data;
        })
        .catch(error => {
          console.error("Error fetching appointments:", error);
        });
    },
    showPrescription(appointment) {
      this.selectedAppointment = appointment;
      this.showPopup = true;
    },
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      const date = new Date(dateString);
      return date.toLocaleDateString('en-GB', options);
    },
    handleOverlayClick() {
      this.showPopup = false;
    },
  },
};
</script>

<style scoped>
.container-fluid {
  padding: 20px;
  height: 100vh;
}

.description-container {
  padding: 15px;
  border: 1px solid #ffffff;
  border-radius: 10px;
  background-color: #f8f9fa;
  margin-bottom: 20px;
}

.appointment-list {
  padding: 20px;
  display: flex;
  flex-wrap: wrap;
  justify-content: start;
}

.card {
  width: 20rem;
  margin: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
  padding: 15px;
}

.card:hover {
  transform: scale(1.05);
}

.btn {
  border-radius: 20px;
}

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}

.popup-container {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  width: 600px;
}

.prescription-container {
  max-width: 100%;
  margin: auto;
  padding: 20px;
}

.header {
  text-align: center;
}

.patient-info, .rx-section {
  margin-top: 20px;
}
</style>
