<template>
    <div>
        <VideoPlayer
            :videoPath="videoPath"
            :quizData="quizData"
            :currentQuizIndex="currentQuizIndex"
            :showQuizOverlay="showQuizOverlay"
            @load-video-data="loadVideoData"
            @show-quiz-overlay="showQuizOverlayHandler"
        />
        <QuizOverlay
            :currentQuestion="currentQuestion"
            :showQuizOverlay="showQuizOverlay"
            @submit-answer="submitAnswer"
            @hide-quiz-overlay="hideQuizOverlayHandler"
        />
    </div>
</template>

<script>
import axios from 'axios';
import serverURL from '@/constants';
import VideoPlayer from './VideoPlayer.vue';
import QuizOverlay from './QuizOverlay.vue';

export default {
    components: {
        VideoPlayer,
        QuizOverlay,
    },
    data() {
        return {
            videoPath: '',
            quizData: [],
            currentQuizIndex: 0,
            showQuizOverlay: false,
            currentQuestion: {},
            renderVideo: false,
        };
    },
    methods: {
        async loadVideoData() {
            try {
                const response = await axios.get(serverURL + '/api/videos/1');
                const pathText = response.data.path;
                this.videoPath = serverURL + '/' + pathText;
                this.renderVideo = true;
                console.log('Video data:', response.data);
                this.quizData = response.data.quiz_questions;
            } catch (error) {
                console.error('Error loading video data:', error);
            }
        },
        showQuizOverlayHandler() {
            this.showQuizOverlay = true;
            this.currentQuestion = this.quizData[this.currentQuizIndex];
            this.correctAnswer = this.currentQuestion.correct_answer;
            this.userAnswer = null;
        },
        async submitAnswer(payload) {
            try {
                await axios.post(serverURL + '/api/user-answers', payload);
                this.currentQuizIndex++;
                this.$refs.videoPlayer.playVideo();
            } catch (error) {
                console.error('Error saving answer:', error);
            }
        },
        hideQuizOverlayHandler() {
            this.showQuizOverlay = false;
        },
    },
};
</script>
