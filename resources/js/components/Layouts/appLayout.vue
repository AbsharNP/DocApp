<template>
  <div class="app-layout">
    <NavBar />
    
    <div class="main-content">
      <router-view></router-view>
    </div>

    <!-- Footer -->
     <br>
    <footer>
      <div class="flex">
        <h6 class="text-center">© 2024 Edoc. All rights reserved.</h6>
        <p class="text-center">Committed to providing exceptional healthcare and patient support.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import NavBar from '../layoutComponents/NavBar.vue';

export default {
  name: 'AppLayout',
  components: {
    NavBar,
  },
};
</script>

<style scoped>
html, body {
  height: 100%; /* Ensure full height for the body */
  margin: 0; /* Remove default margins */
}

.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* Full viewport height */
}

.main-content {
  margin-top: 70px; /* Adjust this value based on your navbar height */
  flex: 1; /* Allow this section to grow and fill the available space */
}

footer {
  background-color: #f8f9fa; /* Footer background color */
  padding: 10px 0; /* Add some padding */
}

.flex {
  display: flex;
  flex-direction: column;
  align-items: center; /* Center align footer content */
}
</style>
