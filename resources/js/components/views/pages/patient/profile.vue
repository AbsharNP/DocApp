<template>
  <div class="container-fluid d-flex justify-content-center align-items-center">
    <div class="user-profile-container">
      

      <transition name="fade">
        <div v-if="!isEditing" class="profile-info text-center">
          <h4 class="text-center">User Profile</h4>
          <div class="profile-pic-container">
            <img
              :src="previewImage || '/path/to/user-default.png'"
              alt="Profile Preview"
              class="profile-preview"
            />
          </div>

          <div class="profile-details">
            <p><strong>Name:</strong> {{ form.name }}</p>
            <p><strong>Email:</strong> {{ form.email }}</p>
            <button class="btn btn-edit" @click="toggleEditMode">Edit Profile</button>
          </div>
        </div>
      </transition>

      <transition name="fade">
        <div v-if="isEditing" class="user-profile-update-container">
          <h3 class="text-center">Update Profile</h3>

          <form @submit.prevent="updateProfile">
            <div class="form-group">

              <img
              :src="previewImage || '/path/to/user-default.png'"
              alt="Profile Preview"
              class="profile-preview"
            />
              <label for="profile_pic">Update Profile Picture</label>
              <input
                type="file"
                @change="handleFileChange"
                id="profile_pic"
                accept="image/*"
              />
            </div>

            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" v-model="form.name" id="name" required />
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" v-model="form.email" id="email" required />
            </div>

            <div class="form-group">
              <label for="password">New Password</label>
              <input
                type="password"
                v-model="form.password"
                id="password"
                placeholder="Leave blank to keep current password"
              />
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
          </form>
        </div>
      </transition>
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
        name: '',
        email: '',
        profile_pic: null,
        password: '',
      },
      previewImage: null,
      isEditing: false, // Track edit mode
    };
  },
  methods: {
    toggleEditMode() {
      this.isEditing = !this.isEditing; // Toggle the edit mode
    },
    handleFileChange(event) {
      const file = event.target.files[0];
      this.form.profile_pic = file;

      if (file) {
        this.previewImage = URL.createObjectURL(file);
      }
    },

    updateProfile() {
      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('email', this.form.email);
      if (this.form.profile_pic) {
        formData.append('profile_pic', this.form.profile_pic);
      }
      if (this.form.password) {
        formData.append('password', this.form.password);
      }

      axios
        .post('/user/update-profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        .then(() => {
          Swal.fire({
            icon: 'success',
            title: 'Profile updated successfully!',
            confirmButtonText: 'OK',
          });
          this.isEditing = false; // Exit edit mode after successful update
        })
        .catch((error) => {
          console.error('Error updating profile:', error);
          Swal.fire({
            icon: 'error',
            title: 'Failed to update profile',
            text: error.response?.data?.message || 'Something went wrong!',
            confirmButtonText: 'Try Again',
          });
        });
    },
  },
  mounted() {
    axios
      .post('/user/profile')
      .then((response) => {
        const user = response.data;
        this.form.name = user.name;
        this.form.email = user.email;
        this.previewImage = user.profile_pic ? `/storage/${user.profile_pic}` : '/images/default-avatar.jpg';
      })
      .catch((error) => {
        console.error('Error fetching profile:', error);
      });
  },
};
</script>

<style scoped>
.container-fluid {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f4f4f4;
}

.user-profile-container {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
}

.profile-info {
  text-align: center;
  margin-bottom: 20px;
}

.profile-pic-container {
  margin-bottom: 20px;
}

.profile-preview {
  display: block;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin: 0 auto 15px;
}

.profile-details {
  margin-bottom: 15px;
}

.btn-edit {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 10px;
}

.btn-edit:hover {
  background-color: #0056b3;
}

.user-profile-update-container {
  margin-top: 20px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 10px;
}

button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  width: 100%;
}

button:hover {
  background-color: #0056b3;
}
</style>
