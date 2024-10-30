<template>
    <div class="prescription-container">
      <div class="header">
        <h1>PRESCRIPTION</h1>
        <p>Treatment Facility</p>
        <div class="facility-info">
          <div class="left">
            <p  class="text-start"><strong>Address:</strong> {{ facilityAddress }}</p>
            <p class="text-start"><strong>Phone:</strong> {{ facilityPhone }}</p>
          </div>
          <div class="right">
            <p><strong>Attending Physician:</strong> Dr. {{ doctorName }}</p>
          </div>
        </div>
      </div>
  
      <div class="patient-info">
        <h2>Patient Info</h2>
        <p><strong>Name:</strong> {{ appointment.name }}</p>
        <span><p><strong>Age:</strong></p>
        <input type="text" v-model="age" class="" required>
        </span>
        <p><strong>Appointment Date:</strong> {{ formatDate(appointment.session_date) }}</p>
      </div>
  
      <div class="rx-section">
        <h3><strong>Prescription</strong></h3>
        <textarea 
          v-model="prescription" 
          placeholder="Enter prescription details here"
          rows="5">
        </textarea>
      </div>
  
      <div class="submit-section">
        <button class="btn btn-primary" @click="confirmSubmission">Submit Prescription</button>
      </div>
    </div>
</template>
  
<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    data() {
        return {
            appointment: {
                name: '',
                phone_number: '',
                session_date: '', // Note: This will be formatted in the display
            },
            doctorId: localStorage.getItem('doctorId'),
            prescription: '',  
            age:'',
            facilityAddress: '123 Clinic Street, City Name',
            facilityPhone: '+1-234-567-890',
            doctorName: localStorage.getItem('userName'), // Attending physician
            doctorPhone: '+1-987-654-321',
        };
    },
    mounted() {
        this.getAppointmentDetails();
    },
    methods: {
        getAppointmentDetails() {
            const appointmentId = this.$route.params.id; // Retrieve the ID from the route parameters
            console.log('Appointment ID:', appointmentId); // Log to check the ID

            if (!appointmentId) {
                console.error("Appointment ID is undefined."); // Handle undefined ID case
                return; // Exit if ID is undefined
            }

            // Make the API call to fetch the appointment details
            axios.post(`/treatment/${appointmentId}`)
                .then(response => {
                    console.log('Response from API:', response);
                    this.appointment = response.data; // Assign the response data
                    console.log('Appointment Details:', this.appointment); // Log details for debugging
                })
                .catch(error => {
                    console.error("Error fetching appointment:", error.response ? error.response.data : error); // Log error details
                });
        },
        confirmSubmission() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to submit the prescription?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitPrescription(); // Call the prescription submission method
                }
            });
        },
        submitPrescription() {
            const prescriptionData = {
                appointmentId: this.$route.params.id,
                prescription: this.prescription,
                age: this.age,
                treatment_status: 1, // Assuming treatment status 1 means completed
            };

            axios.post('/submit-prescription', prescriptionData) // Update the URL for consistency
                .then(response => {
                    Swal.fire(
                        'Submitted!',
                        'Prescription submitted successfully!',
                        'success'
                    );
                    this.$router.push('/appointment');  // Navigate back to the appointments list
                })
                .catch(error => {
                    console.error("Error submitting prescription:", error.response ? error.response.data : error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to submit the prescription. Please try again.',
                    });
                });
        },
        // Format date
        formatDate(dateString) {
            const options = { day: '2-digit', month: '2-digit', year: '2-digit' };
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', options);  // Format date as 'dd/mm/yyyy'
        },
    },
};
</script>

<style scoped>
.prescription-container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

.facility-info {
    display: flex;
    justify-content: space-between;
    padding: 20px 0;
}

.left,
.right {
    width: 48%;
}

.patient-info,
.rx-section {
    margin-bottom: 20px;
}

.rx-section {
    border-top: 1px solid #000;
    padding-top: 10px;
}

textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    margin-top: 10px;
}

.submit-section {
    text-align: right;
}

.btn {
    padding: 10px 20px;
    background-color: #007bff;
    border: none;
    color: white;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3;
}
</style>
