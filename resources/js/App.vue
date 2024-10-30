<template>
   <component :is="layout">
    <div id="app">
     
     
     <Router-View />
    
    </div>
  </component>
  </template>
  
  <script>
  import AuthLayout from './components/Layouts/authLayout.vue';
  import AppLayout from './components/Layouts/appLayout.vue';
  


  
  
  export default {
    name: 'App',
    computed: {
    layout() {
      const layout = this.$route.meta.layout || 'app';
      return layout === 'auth' ? AuthLayout : AppLayout;
    },
  },
  
   
  };
  </script>
  
  