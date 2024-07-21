<template>
  <div class="container">
    <video
      v-if="renderVideo"
      ref="videoPlayer"
      width="640"
      height="360"
      @timeupdate="handleTimeUpdate"
      @loadedmetadata="initializeSeekbar"
    >
      <source :src="videoPath" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
    <div class="controls">
      <button @click="playPause" class="btn play-pause-btn">
        <font-awesome-icon :icon="isPlaying ? ['fas', 'pause'] : ['fas', 'play']" />
      </button>
      
      <input
        type="range"
        min="0"
        :max="videoDuration"
        v-model="seekbarValue"
        @input="onSeekbarInput"
        @mousedown="startSeek"
        @mouseup="endSeek"
        class="custom-seekbar"
      />
      <button @click="toggleMute" class=" mute-btn">
        <font-awesome-icon :icon="isMuted ? ['fas', 'volume-mute'] : ['fas', 'volume-up']" />
      </button>
      <button @click="toggleFullscreen" class=" fullscreen-btn">
        <font-awesome-icon icon="expand" />
      </button>
    </div>
    <div v-if="showQuizOverlay" class="quiz-overlay">
      <p>{{ currentQuestion.question }}</p>
      <form @submit.prevent="submitAnswer">
        <div
          v-for="(option, index) in currentQuestion.options"
          :key="index"
          :class="getOptionClass(index)"
          class="quiz-option"
        >
          <label>
            <input type="radio" :value="index" v-model="selectedOption" /> {{ option }}
            <span class="radio-checkmark"></span>
          </label>
        </div>
        <button v-if="!isSubmitted" class="btn submit-btn" type="submit">Submit</button>
        <button v-else @click="closeOverlay" class="btn done-btn" type="button">Done</button>
      </form>
    </div>
  </div>
</template>



<script>
import axios from "axios";
import serverURL from "@/constants";

export default {
  data() {
    return {
      videoPath: "",
      quizData: [],
      currentQuizIndex: 0,
      showQuizOverlay: false,
      currentQuestion: {},
      selectedOption: null,
      userAnswer: null,
      correctAnswer: null,
      renderVideo: false,
      isSubmitted: false,
      answeredQuestions: [],  // List of answered question IDs
      videoDuration: 0,
      seekbarValue: 0,
      isPlaying: false,  // Track play/pause state
      bufferTime: 10,  // Buffer time to adjust video play position
      isMuted: false,
      seeking: false,
      
    };
  },
  mounted() {
    this.loadVideoData();
    this.loadUserAnswers();
  },
  methods: {
    async loadVideoData() {
      try {
        await axios.get(serverURL + "/api/videos/1").then((response) => {
          let pathText = response.data.path;
          this.videoPath = serverURL + "/" + pathText;
          this.renderVideo = true;
          console.log("Video data:", response.data);
          this.quizData = response.data.quiz_questions;
        });
      } catch (error) {
        console.error("Error loading video data:", error);
      }
    },
    async loadUserAnswers() {
      try {
        await axios.get(serverURL + "/api/user-answers?user_id=1").then((response) => {
          this.answeredQuestions = response.data.map(answer => answer.quiz_question_id);
        });
      } catch (error) {
        console.error("Error loading user answers:", error);
      }
    },
    checkQuiz() {
    const videoPlayer = this.$refs.videoPlayer;

    if (this.currentQuizIndex >= this.quizData.length) return;

    const currentQuestion = this.quizData[this.currentQuizIndex];

    // Check if the quiz question is due for display
    if (videoPlayer.currentTime >= currentQuestion.seconds && !this.answeredQuestions.includes(currentQuestion.id)) {
      videoPlayer.pause();
      this.isPlaying = false;
      this.showQuizOverlay = true;
      this.currentQuestion = currentQuestion;
      this.correctAnswer = currentQuestion.correct_answer;
      this.userAnswer = null;
      this.isSubmitted = false;
    }
  },
  async submitAnswer() {
    const question = this.currentQuestion;
    const answer = this.selectedOption;
    this.userAnswer = answer;
    this.correctAnswer = question.correct_answer;

    try {
      await axios.post(`${serverURL}/api/user-answers`, {
        user_id: 2, // Example user ID
        quiz_question_id: question.id,
        selected_option: answer,
        correct: answer === question.correct_answer,
      });
      this.isSubmitted = true;
      this.answeredQuestions.push(question.id); // Mark the question as answered
    } catch (error) {
      console.error("Error saving answer:", error);
    }
  },
  closeOverlay() {
    this.showQuizOverlay = false;
    this.currentQuizIndex++;
    this.selectedOption = null;
    this.$refs.videoPlayer.play();
    this.isPlaying = true;
  },
  getOptionClass(index) {
    if (this.userAnswer === null) {
      return { "selected-option": this.selectedOption === index };
    }
    if (index === this.correctAnswer) {
      return "correct-option";
    }
    if (index === this.userAnswer) {
      return "incorrect-option";
    }
    return "";
  },

  onSeekbarInput(event) {
  const videoPlayer = this.$refs.videoPlayer;
  const newTime = parseFloat(event.target.value);
  console.log(`Seekbar input: ${newTime}`);
  console.log(`current time (before update): ${videoPlayer.currentTime}`);
  videoPlayer.currentTime = newTime;
  console.log(`current time (after update): ${videoPlayer.currentTime}`);
  this.seekbarValue = newTime;
  this.checkQuiz();
},

  startSeek() {
    this.seeking = true;
  },

  endSeek() {
    this.seeking = false;
    this.checkQuiz(); // Check quiz conditions after seeking
  },

  initializeSeekbar() {
    const videoPlayer = this.$refs.videoPlayer;

    // Initialize seekbar value and video duration
    this.videoDuration = videoPlayer.duration;
    this.seekbarValue = videoPlayer.currentTime;

    // Listen for time updates
    videoPlayer.addEventListener('timeupdate', () => {
      if (!this.seeking) {
        this.seekbarValue = videoPlayer.currentTime;
      }
    });
  },

  playPause() {
    const videoPlayer = this.$refs.videoPlayer;
    if (this.isPlaying) {
      videoPlayer.pause();
    } else {
      videoPlayer.play();
    }
    this.isPlaying = !this.isPlaying;
  },
  toggleMute() {
    const videoPlayer = this.$refs.videoPlayer;
    videoPlayer.muted = !videoPlayer.muted;
    this.isMuted = videoPlayer.muted;
  },
  toggleFullscreen() {
    const videoPlayer = this.$refs.videoPlayer;
    if (videoPlayer.requestFullscreen) {
      videoPlayer.requestFullscreen();
    } else if (videoPlayer.mozRequestFullScreen) { /* Firefox */
      videoPlayer.mozRequestFullScreen();
    } else if (videoPlayer.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
      videoPlayer.webkitRequestFullscreen();
    } else if (videoPlayer.msRequestFullscreen) { /* IE/Edge */
      videoPlayer.msRequestFullscreen();
    }
  },
  updateSeekbar() {
    const videoPlayer = this.$refs.videoPlayer;
    if (!this.seeking) {
      this.seekbarValue = videoPlayer.currentTime;
    }

  },
  handleTimeUpdate(){
  this.updateSeekbar();
  this.checkQuiz();
}
},
};
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: #1e1e1e;
  color: white;
  padding: 20px;
  border-radius: 10px;
}

video {
  border-radius: 10px;
}

.controls {
  display: flex;
  align-items: center;
  gap: 10px; /* Increased gap between buttons */
  margin-top: 10px;
  width: 100%; /* Full width to accommodate the seekbar */
}

.btn {
  background-color: #ff3366;
  color: white;
  border: none;
  padding: 10px 15px; /* Adjusted padding */
  cursor: pointer;
  border-radius: 50%;
}

.mute-btn,
.fullscreen-btn {
  background-color: #1e1e1e;

  color: white;
  border: none;
  padding: 8px; /* Adjusted padding */
  cursor: pointer;
  border-radius: 15px;
}
 .play-pause-btni {
  font-size: 20px; /* Larger icon size */
}

.mute-btn i {
  font-size: 20px; /* Larger icon size */
}

.fullscreen-btn i {
  font-size: 20px; /* Larger icon size */
}

.custom-seekbar {
  -webkit-appearance: none;
  width: 100%; /* Full width of the container */
  height: 8px; /* Increased height for better visibility */
  background: #ff3366; /* Changed seekbar progress color to pink */
  border-radius: 5px;
  outline: none;
  margin: 0;
}

.custom-seekbar::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 12px; /* Smaller thumb */
  height: 12px; /* Smaller thumb */
  background: white;
  border-radius: 50%;
  cursor: pointer;
}

.custom-seekbar::-moz-range-thumb {
  width: 12px; /* Smaller thumb */
  height: 12px; /* Smaller thumb */
  background: white;
  border-radius: 50%;
  cursor: pointer;
}

.quiz-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 20px;
  border: 1px solid #ccc;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  
  text-align: center;
  border-radius: 15px;
}


.quiz-option {
  margin: 5px 0;
  padding: 8px;
}

.quiz-option label {
  display: flex;
  align-items: center;
  padding-left: 20px;
}


.selected-option {
  background-color: #ff3366;
}

.correct-option {
  background-color: #28a745;
}

.incorrect-option {
  background-color: #dc3545;
}

.submit-btn, .done-btn {
  background-color: #ff3366;
}

.submit-btn:hover, .done-btn:hover {
  background-color: #e02f5f;
}
</style>