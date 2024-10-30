<template>
  <section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
      <div class="row d-flex align-items-center justify-content-center h-100">
        <div class="col-md-8 col-lg-7 col-xl-6">
          <object type="image/svg+xml" data="/svg/login.svg" class="img-fluid" alt="Phone image" />
        </div>
        <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
          <form @submit.prevent="login" class="login-form">
            <h3 class="text-center mb-4">Sign In</h3>

            <!-- General error message -->
            <div v-if="errors.general" class="text-danger mb-3 text-center">
              {{ errors.general }}
            </div>

            <div class="form-outline mb-4">
              <input
                type="email"
                v-model="email"
                class="form-control form-control-lg"
                id="form1Example13"
                required
              />
              <label class="form-label" for="form1Example13">Email address</label>
            </div>

            <div class="form-outline mb-4">
              <input
                type="password"
                v-model="password"
                class="form-control form-control-lg"
                id="form1Example23"
                required
              />
              <label class="form-label" for="form1Example23">Password</label>
              <div v-if="errors.password" class="text-danger text-center">{{ errors.password[0] }}</div>
              <div v-if="errors.email" class="text-danger text-center">{{ errors.email [0]}}</div>

            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="form1Example3"
                  v-model="remember"
                />
                <label class="form-check-label" for="form1Example3"> Remember me </label>
              </div>
              <!-- <a href="#" class="forgot-password">Forgot password?</a> -->
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>

            <p class="p-3 text-center">
              Create an account? <router-link to="/SignUp">Register</router-link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginPage',
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      errors: {}, // To hold validation errors and general errors
    };
  },
  methods: {
    async login() {
      this.errors = {};

      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password,
          remember: this.remember,
        });

        const userType = response.data.user.user_type;
        const userName = response.data.user.name;
        const userEmail = response.data.user.email;
        const userId = response.data.user.id;
        const proPic = response.data.user.profile_pic;

        localStorage.setItem('isAuthenticated', 'true');
        localStorage.setItem('userId', userId);
        localStorage.setItem('userType', userType);
        localStorage.setItem('userName', userName);
        localStorage.setItem('userEmail', userEmail);
        localStorage.setItem('profile_pic', proPic);

        if (userType === 'doctor') {
          const doctorResponse = await axios.post(`/doctors-by-email/${userEmail}`);
          const doctorId = doctorResponse.data.id;
          localStorage.setItem('doctorId', doctorId);

          this.$router.push('/doctor-dash');
        } else if (userType === 'admin') {
          this.$router.push('/admin');
        } else if (userType === 'patient') {
          this.$router.push('/patient-dash');
        }
      } catch (error) {
        if (error.response && error.response.data.errors) {
          // Handle validation errors (object-based)
          this.errors = error.response.data.errors;
        } else if (error.response && Array.isArray(error.response.data)) {
          // Handle errors when it's an array like ["The provided credentials are incorrect."]
          const errorMessage = error.response.data[0];
          this.errors.general = errorMessage;
        } else {
          alert('Login failed. Please try again.');
        }
      }
    },
  },
};
</script>

<style scoped>
.gradient-custom {
  background: linear-gradient(135deg, #a8edea 10%, #fed6e3 100%);
}

.login-form {
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h3 {
  color: #333;
}

.form-outline {
  position: relative;
  margin-bottom: 20px;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
}

.forgot-password {
  color: #007bff;
  text-decoration: none;
}

.forgot-password:hover {
  text-decoration: underline;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

.text-danger {
  font-size: 0.875rem;
}
</style>
