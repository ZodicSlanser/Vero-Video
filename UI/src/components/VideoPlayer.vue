<template>
    <div class="container">
        <video ref="videoPlayer" width="640" height="360" controls @timeupdate="checkQuiz">
            <source :src="videoPath" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</template>

<script>
export default {
    props: {
        videoPath: String,
        quizData: Array,
        currentQuizIndex: Number,
        showQuizOverlay: Boolean,
    },
    mounted() {
        this.$emit('load-video-data');
    },
    methods: {
        checkQuiz() {
            const videoPlayer = this.$refs.videoPlayer;
            if (
                this.currentQuizIndex < this.quizData.length &&
                videoPlayer.currentTime >= this.quizData[this.currentQuizIndex].seconds
            ) {
                videoPlayer.pause();
                this.$emit('show-quiz-overlay');
            }
        },
        playVideo() {
            this.$refs.videoPlayer.play();
        },
    },
};
</script>

<style scoped>
.container {
    width: 70%;
}

video {
    width: 100%;
    height: auto;
    display: block;
}
</style>
