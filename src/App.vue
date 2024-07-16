<template>
    <div id="app">
        <VideoQuiz />
    </div>
</template>

<script>
import VideoQuiz from './components/MainContainer.vue';


export default {
    name: 'App',
    components: {
        VideoQuiz,
    },
};
</script>

<style>
#app {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
</style>
