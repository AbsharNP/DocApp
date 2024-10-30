<template>
  <div class="container-fluid vh-100 d-flex flex-column">
    <div class="row justify-content-center my-4">
      <div class="col-lg-8">
        <div v-if="error" class="alert alert-danger text-center">{{ response.message }}</div>
        
        <div v-if="doctor" class="doctor-info mb-3 card p-3">
          <h5 class="card-title">Doctor Information</h5>
          <p><strong>Name:</strong> {{ doctor.name }}</p>
          <p><strong>Email:</strong> {{ doctor.email }}</p>
          <p><strong>Phone Number:</strong> {{ doctor.phone_number }}</p>
        </div>

        <div v-if="!filteredSessions.length && !error" class="alert alert-info text-center">
          <p>No available sessions at this time. Please check back later.</p>
        </div>

        <div class="card" v-if="filteredSessions.length">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                  <tr>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>No. of Sessions</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="session in filteredSessions" :key="session.id">
                    <td>{{ formatDate(session.session_date) }}</td>
                    <td>{{ formatTime(session.start_time) }}</td>
                    <td>{{ formatTime(session.end_time) }}</td>
                    <td>{{ session.no_of_sessions }}</td>
                    <td>
                      <button class="btn btn-primary btn-sm" @click="submitBooking(session.id)">Book</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  setup() {
    const route = useRoute();
    const sessions = ref([]);
    const error = ref(null);
    const doctor = ref(null);
    const doctorId = ref(route.query.doctorId);

    onMounted(() => {
      fetchSessions();
    });

    const fetchSessions = async () => {
      try {
        const response = await axios.post(`/sessions/${doctorId.value}`);
        const { doctor: doctorData, sessions: allSessions } = response.data;

        allSessions.sort((a, b) => new Date(a.session_date) - new Date(b.session_date));

        doctor.value = doctorData;
        sessions.value = allSessions;
      } catch (err) {
        error.value = 'Failed to load sessions. Please try again.';
      }
    };

    const filteredSessions = computed(() => {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      return sessions.value.filter(session => {
        const sessionDate = new Date(session.session_date);
        return sessionDate >= today;
      });
    });

    const formatDate = (date) => new Date(date).toLocaleDateString();
    const formatTime = (time) => {
      const [hours, minutes] = time.split(':');
      const date = new Date();
      date.setHours(hours, minutes);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    };

    const submitBooking = async (sessionId) => {
      const userId = localStorage.getItem('userId');
      const userName = localStorage.getItem('userName');

      if (!doctor.value) {
        Swal.fire('Error', 'Doctor information is not available', 'error');
        return;
      }

      try {
        const response = await axios.post('/appointments', {
          user_id: userId,
          name: userName,
          session_id: sessionId,
          dr_name: doctor.value.name,
          doctor_id: doctorId.value
        });

        Swal.fire('Success', 'Appointment booked successfully', 'success');
      } catch (error) {
        Swal.fire('Error', error.response.data.message || 'Failed to book appointment', 'error');
      }
    };

    return {
      sessions,
      error,
      doctor,
      filteredSessions,
      formatDate,
      formatTime,
      submitBooking,
    };
  }
};
</script>

<style scoped>
.container-fluid {
  padding: 20px;
}

.card {
  margin-bottom: 20px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.table-bordered {
  border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid #dee2e6;
}

.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}

.thead-light th {
  background-color: #f8f9fa;
  color: #495057;
}
</style>
