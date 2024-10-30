<template>
  <div class="container-fluid vh-100 p-4">
    <!-- Cards and Table Row -->
    <div class="row">
      <!-- Department Table Container (left) -->
      <div class="col-md-6 mb-4">
        <div class="shadow-lg p-3 mb-5 bg-white rounded">
          <h5 class="text-center mb-4">Departments</h5>

          <!-- Add Department Button -->
          <button class="btn btn-outline-primary mb-3" @click="showAddDepartmentModal">
            <svg xmlns="http://www.w3.org/2000/svg" 
              width="24" height="24" viewBox="0 0 24 24" 
              fill="none" stroke="currentColor" stroke-width="2" 
              stroke-linecap="round" stroke-linejoin="round" 
              class="feather feather-plus">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Department
          </button>

          <!-- Department Table -->
          <div class="table-container">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th style="width: 40px;">ID</th>
                  <th style="width: 200px;">Name</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(department, index) in departments" :key="department.id">
                  <td>{{ index + 1 }}</td>
                  <td class="d-flex justify-content-between align-items-center">
                    <span>{{ department.name }}</span>
                    <button class="btn btn-outline-danger btn-sm" @click="confirmDeleteDepartment(department.id)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Doctor Table Container (right) -->
      <div class="col-md-6 mb-4">
        <div class="shadow-lg p-3 mb-5 bg-white rounded">
          <h5 class="text-center mb-4">Doctors</h5>

          <!-- Add Doctor Button -->
          <div class="d-flex justify-content-end mb-3">
            <router-link to="/add-doctor">
              <button class="btn btn-outline-primary add-doctor-button">
                <svg xmlns="http://www.w3.org/2000/svg" 
                  width="24" height="24" viewBox="0 0 24 24" 
                  fill="none" stroke="currentColor" stroke-width="2" 
                  stroke-linecap="round" stroke-linejoin="round" 
                  class="feather feather-plus">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Doctor
              </button>
            </router-link>
          </div>

          <!-- Doctors Table -->
          <div class="table-container">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Department</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="doctor in doctors" :key="doctor.id">
                  <td>{{ doctor.name }}</td>
                  <td>{{ doctor.email }}</td>
                  <td>{{ doctor.department ? doctor.department.name : 'N/A' }}</td>
                  <td>
                    <button class="btn btn-info btn-sm" @click="showDoctorDetails(doctor)">View Details</button>
                    <button class="btn btn-danger btn-sm" @click="confirmDeleteDoctor(doctor.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>
        </div>
      </div>
    </div>

    <!-- Appointments Overview Container -->
    <div class="row">
      <div class="col-md-12">
        <div class="shadow-lg  mb-5 bg-white rounded">
          <h5 class="text-center mb-4">Appointments Overview</h5>

          <!-- Chart -->
          <div class="chart-container d-flex justify-content-center">
            <apexchart
              width="500"
              type="donut"
              :options="options"
              :series="series"
            ></apexchart>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Adding Department -->
    <div class="modal fade" :class="{ show: isModalVisible }"
         :style="{ display: isModalVisible ? 'block' : 'none' }"
         tabindex="-1" role="dialog"
         @click.self="closeAddDepartmentModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Add Department</h5>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addDepartment">
              <div class="form-group">
                <label for="departmentName">Department Name</label>
                <input
                  type="text"
                  id="departmentName"
                  v-model="depName"
                  class="form-control"
                  required
                />
              </div>
              <div class="text-center mt-3">
                <button type="submit" class="btn btn-outline-primary">Add</button>
                <span class="pd"></span>
                <button type="button" class="btn btn-outline-danger" @click="closeAddDepartmentModal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Doctor Details -->
    <div class="modal fade" :class="{ show: isDoctorModalVisible }"
         :style="{ display: isDoctorModalVisible ? 'block' : 'none' }"
         tabindex="-1" role="dialog"
         @click.self="closeDoctorModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Doctor Details</h5>
            <button type="button" class="close" @click="closeDoctorModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><strong>Name:</strong> {{ selectedDoctor.name }}</p>
            <p><strong>Doctor ID:</strong> {{ selectedDoctor.doc_id }}</p>
            <p><strong>Email:</strong> {{ selectedDoctor.email }}</p>
            <p><strong>Phone Number:</strong> {{ selectedDoctor.phone_number }}</p>
            <p><strong>Department:</strong> {{ selectedDoctor.department ? selectedDoctor.department.name : 'N/A' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" @click="closeDoctorModal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Backdrop for Modal -->
    <div v-if="isModalVisible || isDoctorModalVisible" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';
import Swal from 'sweetalert2';

export default {
  name: 'AdminHome',
  components: {
    apexchart: VueApexCharts,
  },
  data() {
    return {
      departments: [],
      doctors: [],
      isModalVisible: false,
      isDoctorModalVisible: false,
      selectedDoctor: {},
      depName: '',
      selectedDepartmentId: null,
      error: null,
      options: {
        chart: {
          id: 'appointments-chart',
          title: {
            text: 'Appointment Status Overview',
            align: 'center',
            style: {
              fontSize: '20px',
              fontWeight: 'bold',
              color: '#333',
            },
          },
        },
        labels: ['Completed', 'Not Completed'],
        colors: ['#28a745', '#dc3545'],
      },
      series: [],
    };
  },
  methods: {
    fetchAppointmentStats() {
      axios.post('/analyse')
        .then(response => {
          const { completed, not_completed } = response.data;
          this.series = [completed, not_completed];
        })
        .catch(error => {
          console.error('Error fetching appointment stats:', error);
        });
    },
    showAddDepartmentModal() {
      this.isModalVisible = true;
    },
    closeAddDepartmentModal() {
      this.isModalVisible = false;
      this.depName = '';
      this.selectedDepartmentId = null;
    },
    fetchDepartments() {
      axios.post('/departments')
        .then((response) => {
          this.departments = response.data;
        })
        .catch((error) => {
          console.error('Error fetching departments:', error);
        });
    },
    fetchDoctors() {
      axios.post('/doctors')
        .then((response) => {
          this.doctors = response.data;
        })
        .catch((error) => {
          console.error('Error fetching doctors:', error);
          this.error = 'An error occurred while fetching doctors.';
        });
    },
    addDepartment() {
      axios.post('/add-department', {
        name: this.depName,
      })
        .then(() => {
          Swal.fire('Success', 'Department added successfully.', 'success');
          this.fetchDepartments();
          this.closeAddDepartmentModal();
        })
        .catch((error) => {
          console.error('Error adding department:', error);
        });
    },
    confirmDeleteDepartment(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the department and all associated data.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteDepartment(id);
        }
      });
    },
    deleteDepartment(id) {
      axios.delete(`/depdlt/${id}`)
        .then(() => {
          Swal.fire('Deleted', 'Department has been deleted.', 'success');
          this.fetchDepartments();
        })
        .catch((error) => {
          console.error('Error deleting department:', error);
        });
    },
    confirmDeleteDoctor(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the doctor and all associated data.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteDoctor(id);
        }
      });
    },
    deleteDoctor(id) {
      axios.delete(`/doc/${id}`)
        .then(() => {
          Swal.fire('Deleted', 'Doctor has been deleted.', 'success');
          this.fetchDoctors();
        })
        .catch((error) => {
          console.error('Error deleting doctor:', error);
        });
    },
    showDoctorDetails(doctor) {
      this.selectedDoctor = doctor;
      this.isDoctorModalVisible = true;
    },
    closeDoctorModal() {
      this.isDoctorModalVisible = false;
      this.selectedDoctor = {};
    },
  },
  mounted() {
    this.fetchDepartments();
    this.fetchDoctors();
    this.fetchAppointmentStats();
  },
};
</script>

<style scoped>
.container-fluid {
  padding: 20px;
}
.table-container {
  max-height: 400px;
  overflow-y: auto;
}
.add-doctor-button svg {
  margin-right: 8px;
}
.modal-backdrop {
  z-index: 1040;
  background-color: rgba(0, 0, 0, 0.5);
}
.modal {
  z-index: 1050;
}
</style>
