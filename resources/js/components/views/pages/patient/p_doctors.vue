<template>
    <div class="container-fluid vh-100 d-flex flex-column">
      <div class="row justify-content-center my-4">
        <div class="col-lg-8">
          <div class="input-group mb-3">
            <input
              v-model="searchQuery"
              type="text"
              class="form-control"
              placeholder="Search by doctor name or department"
              aria-label="Search"
            />
            <button class="btn btn-primary" type="button" @click="searchDoctors">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>
  
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="row">
            <div class="col-md-6 mb-4" v-for="doctor in filteredDoctors" :key="doctor.id">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title">{{ doctor.name }}</h5>
                  <p class="card-text">
                    <strong>Doctor ID:</strong> {{ doctor.doc_id }}<br />
                    <strong>Email:</strong> {{ doctor.email }}<br />
                    <strong>Phone Number:</strong> {{ doctor.phone_number }}<br />
                    <strong>Department:</strong> {{ doctor.department ? doctor.department.name : 'N/A' }}
                  </p>
                  <button class="btn btn-outline-primary" @click="getAppointment(doctor.id)">
                    Get Appointment
                  </button>
                </div>
              </div>
            </div>
            <div v-if="filteredDoctors.length === 0" class="col-12 text-center">
              <div class="alert alert-info">No doctors found</div>
            </div>
          </div>
  
          <div v-if="error" class="alert alert-danger mt-4">{{ error }}</div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  
  export default {
    setup() {
      const router = useRouter();
      const doctors = ref([]);
      const searchQuery = ref('');
      const error = ref(null);
  
      onMounted(() => {
        fetchDoctors();
      });
  
      const fetchDoctors = async () => {
        try {
          const response = await axios.post('/doctor');
          doctors.value = response.data;
        } catch (error) {
          console.error('Error fetching doctors:', error);
          error.value = 'Failed to load doctors. Please try again.';
        }
      };
  
      const filteredDoctors = computed(() => {
        return doctors.value.filter(doctor => {
          const searchTerm = searchQuery.value.toLowerCase();
          return (
            doctor.name.toLowerCase().includes(searchTerm) ||
            (doctor.department && doctor.department.name.toLowerCase().includes(searchTerm))
          );
        });
      });
  
      const getAppointment = async (doctorId) => {
        try {
          await axios.get(`/sessions/${doctorId}`);
          router.push({ path: '/available-sessions', query: { doctorId: doctorId.toString() } });
        } catch (error) {
          console.error('Error fetching sessions:', error);
          error.value = 'Failed to load sessions. Please try again.';
        }
      };
  
      return {
        doctors,
        searchQuery,
        filteredDoctors,
        error,
        getAppointment,
      };
    },
  };
  </script>
  
  <style scoped>
  .container-fluid {
    padding: 20px; /* Add padding to the container */
  }
  
  .card {
    border: 1px solid #e3e3e3; /* Light border for cards */
    transition: transform 0.2s; /* Add transition for hover effect */
  }
  
  .card:hover {
    transform: translateY(-5px); /* Slight lift on hover */
  }
  
  .btn-outline-primary {
    border-color: #007bff; /* Customize button border color */
  }
  
  .btn-outline-primary:hover {
    background-color: #007bff; /* Button background color on hover */
    color: white; /* Change text color on hover */
  }
  
  .alert {
    margin-top: 20px; /* Margin for alerts */
  }
  </style>
  