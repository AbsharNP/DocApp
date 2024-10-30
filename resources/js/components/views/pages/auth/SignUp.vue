<template>
  <section class="vh-100" style="background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-6 col-xl-5">
          <div class="card text-black shadow-lg" style="border-radius: 15px;">
            <div class="card-body p-4">
              <div class="row justify-content-center">
                <div class="col-12">

                  <h2 class="text-center fw-bold mb-4">Sign Up</h2>

                  <form @submit.prevent="handleSubmit">

                    <!-- Name Input -->
                    <div class="mb-3">
                      <label class="form-label fw-bold" for="nameInput">Name</label>
                      <input type="text" id="nameInput" class="form-control form-control-lg" v-model="name" placeholder="Your Name" required />
                      <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                      <label class="form-label fw-bold" for="emailInput">Email</label>
                      <input type="email" id="emailInput" class="form-control form-control-lg" v-model="email" placeholder="Your Email" required />
                      <div v-if="errors.email" class="text-danger mt-1">{{ errors.email[0] }}</div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                      <label class="form-label fw-bold" for="passwordInput">Password</label>
                      <input type="password" id="passwordInput" class="form-control form-control-lg" v-model="password" placeholder="Password" required />
                      <div v-if="errors.password" class="text-danger mt-1">{{ errors.password[0] }}</div>
                    </div>

                    <!-- Repeat Password Input -->
                    <div class="mb-3">
                      <label class="form-label fw-bold" for="repeatPasswordInput">Confirm Password</label>
                      <input type="password" id="repeatPasswordInput" class="form-control form-control-lg" v-model="repeatpassword" placeholder="Repeat Password" required />
                      <div v-if="errors.repeatpassword" class="text-danger mt-1">{{ errors.repeatpassword }}</div>
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="form-check d-flex justify-content-start mb-3">
                      <input class="form-check-input me-2" type="checkbox" id="termsCheck" required />
                      <label class="form-check-label" for="termsCheck">
                        I agree to the <a href="#">Terms of Service</a>
                      </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-center">
                      <button type="submit" class="btn btn-gradient btn-lg px-4" style="border-radius: 50px;">
                        Register
                      </button>
                    </div>

                    <!-- Redirect to Login -->
                    <p class="text-center mt-3">Already have an account? 
                      <router-link to="/login" class="fw-bold" style="text-decoration: none; color: #007bff;">Sign In</router-link>
                    </p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      repeatpassword: '',
      errors: {},
    };
  },
  methods: {
    async handleSubmit() {
      this.clearErrors();

      if (this.password !== this.repeatpassword) {
        this.errors.repeatpassword = 'Passwords do not match';
        return;
      }

      const data = {
        name: this.name,
        email: this.email,
        password: this.password,
        password_confirmation: this.repeatpassword,
      };

      try {
        const response = await axios.post('/register', data);

        Swal.fire({
          icon: 'success',
          title: 'Registration Successful!',
          text: 'You can now log in with your new account.',
          confirmButtonText: 'Login',
        }).then(() => {
          this.$router.push('/login');
        });
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: error.message,
          });
        }
      }
    },
    clearErrors() {
      this.errors = {};
    }
  }
};
</script>

<style scoped>
.vh-100 {
  height: 100vh;
}

.card {
  background-color: white;
  border: none;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-control-lg {
  border-radius: 10px;
  background-color: #f9f9f9;
  border: 1px solid #ced4da;
  padding: 10px;
  font-size: 1rem;
}

.form-control-lg:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
  background-color: #fff;
}

.btn-gradient {
  background: linear-gradient(to right, #74ebd5, #ACB6E5);
  color: white;
  border: none;
  transition: background 0.3s ease-in-out;
}

.btn-gradient:hover {
  background: linear-gradient(to right, #67d6c6, #9ba8ce);
}

.text-danger {
  font-size: 0.875rem;
}

a {
  color: #007bff;
}

a:hover {
  text-decoration: underline;
}
</style>
  