<template>
    <div>
      <h1>Home Page</h1>
      <p>Welcome to the Home component.</p>
    </div>
  </template>
  
  <script>
  export default {
    name: "Hello",
    mounted() {
      console.log("Hello component has been mounted!");
    }
  };
  </script>
  
  <style scoped>
  h1 {
    color: #42b983;
  }
  </style>