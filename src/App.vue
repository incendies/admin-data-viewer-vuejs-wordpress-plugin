<template>
    <div id="app">
        <nav>
    <ul>
        <li><router-link to="/table">Table</router-link></li>
        <li><router-link to="/graph">Graph</router-link></li>
        <li><router-link to="/settings">Settings</router-link></li>
    </ul>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "App"
};
</script>

<style scoped>
nav {
    padding: 10px;
    background-color: #f8f9fa;
}

ul {
    list-style: none;
    padding: 0;
}

li {
    display: inline;
    margin-right: 15px;
}
</style>