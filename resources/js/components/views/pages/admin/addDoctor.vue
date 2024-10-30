<template>
  <div class="container-fluid vh-100 p-4">
  <div>
    <!-- Navigation Bar
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="#">Doctor Registration</a>
      </div>
      
      <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item active">
              <a class="nav-link" href="/admin">Home</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href=""  @click="logout">Logout</a>
            </li>
          </ul>
        </div>
    </nav> -->

    <div class="register-doctor-container container mt-5">
      <div class="form-card shadow-lg p-4 rounded">
        <div class="d-flex justify-content-start mb-3">
          <router-link to="/admin" class="btn btn-outline-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 width="20" 
                 height="20" 
                 viewBox="0 0 24 24" 
                 fill="none" 
                 stroke="currentColor" 
                 stroke-width="2" 
                 stroke-linecap="round" 
                 stroke-linejoin="round" 
                 class="feather feather-arrow-left-circle me-2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 8 8 12 12 16"></polyline>
              <line x1="16" y1="12" x2="8" y2="12"></line>
            </svg>
            Back
          </router-link>
        </div>

        <!-- Form Heading -->
        <h2 class="text-center mb-4">Register Doctor</h2>

        <form @submit.prevent="registerDoctor" class="needs-validation" novalidate>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Doctor's Name</label>
              <input
                type="text"
                v-model="form.name"
                id="name"
                class="form-control"
                placeholder="Enter doctor's name"
                required
              />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                v-model="form.email"
                id="email"
                class="form-control"
                placeholder="Enter email"
                required
              />
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input
                type="tel"
                v-model="form.phone_number"
                id="phone"
                class="form-control"
                placeholder="Enter phone number"
                required
              />
              <div class="invalid-feedback">Please enter a valid phone number.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="department" class="form-label">Department</label>
              <select
                v-model="form.department"
                id="department"
                class="form-select"
                required
              >
                <option value="" disabled>Select department</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
              <div class="invalid-feedback">Please select a department.</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                v-model="form.password"
                id="password"
                class="form-control"
                placeholder="Enter password"
                required
              />
              <div class="invalid-feedback">Please enter a password.</div>
            </div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block mt-3">Register</button>
          </div>
        </form>
      </div>
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
      form: {
        name: "",
        email: "",
        phone_number: "",
        department: "",
        password: "",
      },
      departments: [],
    };
  },
  mounted() {
    this.fetchDepartments();
  },
  methods: {
    fetchDepartments() {
  axios.post('/departments')
    .then(response => {
      console.log('Departments fetched:', response.data); 
      this.departments = response.data; 
    })
    .catch(error => {
      console.error('Error fetching departments:', error);
    });
},
    registerDoctor() {
      axios.post('/add-doctor', this.form) 
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Doctor registered successfully',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            padding: '2em',
          });
          this.resetForm(); // Reset the form after successful registration
          this.$router.push('/admin'); // Redirect to the doctor view page
        })
        .catch(error => {
          console.error('Error registering doctor:', error);
          Swal.fire({
            icon: 'error',
            title: 'Registration failed',
            text: error.response.data.message || 'An error occurred. Please try again.',
          });
        });
    },
    resetForm() {
      this.form.name = "";
      this.form.email = "";
      this.form.phone = "";
      this.form.department = "";
      this.form.password = "";
    },
    async logout() {
      try {
        await axios.post('/logout');
        
        // Clear local storage or session data if required
        localStorage.removeItem('authToken');
        localStorage.removeItem('userName');
        localStorage.removeItem('userEmail');
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('userId');
        localStorage.removeItem('userType');

        // Redirect the user to the login page
        this.$router.push({ name: 'login' });

        // Optionally, show a success message (if using a toast system)
        this.$toast.success('Logged out successfully');
      } catch (error) {
        console.error('Error logging out:', error);
        this.$toast.error('Error logging out');
      }
    }
  },
};
</script>

<style scoped>
/* Navigation Bar */
.navbar {
  margin-bottom: 30px;
}

/* Main Container */
.register-doctor-container {
  max-width: 800px;
  margin: 0 auto;
  padding-top: 20px;
}

/* Form Card */
.form-card {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 8px;
  transition: box-shadow 0.3s ease;
}

/* Card Hover Effect */
.form-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* Form Labels */
.form-label {
  font-weight: bold;
  color: #495057;
}

/* Form Controls */
.form-control, 
.form-select {
  border-radius: 6px;
  transition: border-color 0.3s ease;
}

/* Focused Input */
.form-control:focus, 
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Submit Button */
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  padding: 10px 20px;
  border-radius: 8px;
  width: 100%;
  transition: background-color 0.3s ease;
}

/* Hover effect for button */
.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

/* Back Button */
.btn-outline-secondary {
  font-size: 14px;
}

/* Responsive Design */
@media (max-width: 576px) {
  .register-doctor-container {
    padding: 10px;
  }

  .btn-primary {
    width: auto;
    display: block;
    margin: 0 auto;
  }
}
</style>
