<template>
  <div class="video-container" ref="videoContainer">
    <video
      v-if="renderVideo"
      ref="videoPlayer"
      width="100%"
      height="100%"
      @timeupdate="handleTimeUpdate"
      @loadedmetadata="onVideoLoaded"
    >
      <source :src="videoPath" type="video/mp4" />
      <track
        v-for="(subtitle, index) in subtitles"
        :key="index"
        :label="subtitle.label"
        kind="subtitles"
        :srclang="subtitle.language"
        :src="subtitle.path"
        :default="index === selectedSubtitleIndex"
      />
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
      <button @click="toggleMute" class="mute-btn">
        <font-awesome-icon
          :icon="isMuted ? ['fas', 'volume-mute'] : ['fas', 'volume-up']"
        />
      </button>
      <button @click="toggleFullscreen" class="fullscreen-btn">
        <font-awesome-icon icon="expand" />
      </button>
      <button @click="toggleSubtitles" class="subtitles-btn">
        <font-awesome-icon
          :icon="subtitlesEnabled ? ['fas', 'closed-captioning'] : ['fas', 'closed-captioning']"
        />
      </button>
    </div>
    <div v-if="showQuizOverlay" class="quiz-overlay">
      <div class="quiz-content">
        <p>{{ currentQuestion.question }}</p>
        <form @submit.prevent="submitAnswer">
          <div class="quiz-options">
            <div
              v-for="(option, index) in currentQuestion.options"
              :key="index"
              :class="['quiz-option', getOptionClass(index)]"
              @click="selectOption(index)"
            >
              <span>{{ option }}</span>
            </div>
          </div>
          <button v-if="!isSubmitted" class="btn submit-btn" type="submit">Submit</button>
          <button v-else @click="closeOverlay" class="btn done-btn" type="button">
            Done
          </button>
        </form>
      </div>
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
      subtitlesPath: "",
      quizData: [],
      currentQuizIndex: 0,
      showQuizOverlay: false,
      currentQuestion: {},
      selectedOption: null,
      userAnswer: null,
      correctAnswer: null,
      renderVideo: false,
      isSubmitted: false,
      answeredQuestions: [],
      videoDuration: 0,
      seekbarValue: 0,
      isPlaying: false,
      bufferTime: 10,
      isMuted: false,
      seeking: false,
      subtitlesEnabled: true, // Track subtitle state
      subtitles: [
        {
          label: "English",
          language: "en",
          path: "/subtitles/sample-en.vtt",
        },
        {
          label: "Spanish",
          language: "es",
          path: "/subtitles/sample-es.vtt",
        },
      ],
      selectedSubtitleIndex: 0,
    };
  },
  mounted() {
    this.loadVideoData();
    this.loadUserAnswers();
    this.$nextTick(() => {
    console.log('Video Player Ref:', this.$refs.videoPlayer);
  });
  },
  methods: {
    async loadVideoData() {
      try {
        const response = await axios.get(`${serverURL}/api/videos/1`);
        this.videoPath = `${serverURL}/api/stream/${response.data.path}`;
        console.log(response.data)

        // Check if the response has a subtitle path
        if (response.data.subtitles_path) {
          this.subtitlesPath = serverURL + "/" + response.data.subtitles_path;
        }

        this.renderVideo = true;
        this.quizData = response.data.quiz_questions;
        this.$nextTick(() => {
        const videoPlayer = this.$refs.videoPlayer;
        if (videoPlayer) {
          console.log('Video Player Ref:', videoPlayer);
        } else {
          console.log('Video Player Ref is undefined');
        }
      });
    } catch (error) {
      console.error("Error loading video data:", error);
    }
  },
    async loadUserAnswers() {
      try {
        const response = await axios.get(serverURL + "/api/user-answers?user_id=1");
        this.answeredQuestions = response.data.map((answer) => answer.quiz_question_id);
      } catch (error) {
        console.error("Error loading user answers:", error);
      }
    },
    onVideoLoaded() {
  this.initializeSeekbar();
  this.initializeSubtitles();

  const videoPlayer = this.$refs.videoPlayer;
  if (videoPlayer) {
    console.log("Video loaded with duration:", videoPlayer.duration);
  }
},
initializeSeekbar() {
  const videoPlayer = this.$refs.videoPlayer;
  if (videoPlayer) {
    this.videoDuration = videoPlayer.duration;
    this.seekbarValue = videoPlayer.currentTime;

    videoPlayer.addEventListener("timeupdate", () => {
      this.checkQuiz();
    })
  }
},  
    initializeSubtitles() {
      const videoPlayer = this.$refs.videoPlayer;
      if (videoPlayer) {
        const tracks = videoPlayer.textTracks;
        for (let i = 0; i < tracks.length; i++) {
          tracks[i].mode = i === this.selectedSubtitleIndex ? "showing" : "hidden";
        }
      }
    },
    selectOption(index) {
      if (!this.isSubmitted) {
        this.selectedOption = index;
      }
    },
    toggleSubtitles() {
      const videoPlayer = this.$refs.videoPlayer;
      if (videoPlayer) {
        this.selectedSubtitleIndex =
          (this.selectedSubtitleIndex + 1) % this.subtitles.length;

        const tracks = videoPlayer.textTracks;
        for (let i = 0; i < tracks.length; i++) {
          tracks[i].mode = i === this.selectedSubtitleIndex ? "showing" : "hidden";
        }
      }
    },
    toggleFullscreen() {
      const videoContainer = this.$refs.videoContainer;
      if (videoContainer.requestFullscreen) {
        videoContainer.requestFullscreen();
      } else if (videoContainer.mozRequestFullScreen) {
        videoContainer.mozRequestFullScreen();
      } else if (videoContainer.webkitRequestFullscreen) {
        videoContainer.webkitRequestFullscreen();
      } else if (videoContainer.msRequestFullscreen) {
        videoContainer.msRequestFullscreen();
      }
    },
    checkQuiz() {
      console.log("Checking Quiz");
      console.log(this.$refs.videoPlayer);
      const videoPlayer = this.$refs.videoPlayer;

      console.log("Current time" + videoPlayer.currentTime);


      if (this.currentQuizIndex >= this.quizData.length) return;

      const currentQuestion = this.quizData[this.currentQuizIndex];
      if (
        videoPlayer.currentTime >= currentQuestion.seconds &&
        !this.answeredQuestions.includes(currentQuestion.id)
      ) {
        videoPlayer.pause();
        videoPlayer.currentTime = currentQuestion.seconds
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
          user_id: 1,
          quiz_question_id: question.id,
          selected_option: answer,
          correct: answer === question.correct_answer,
        });
        this.isSubmitted = true;
        this.answeredQuestions.push(question.id);
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
  if (videoPlayer) {
    const newTime = parseFloat(event.target.value);

    // Prevent setting the time if it's within a quiz time window
    const quizTimes = this.quizData.map(q => q.seconds);
    if (!quizTimes.includes(Math.floor(newTime))) {
      videoPlayer.currentTime = newTime;
      this.seekbarValue = newTime;
    }
  }
},

startSeek() {
  this.seeking = true;

  // Check if the seeking time is within a quiz time window
  const videoPlayer = this.$refs.videoPlayer;
  if (videoPlayer) {
    const seekingTime = videoPlayer.currentTime;

    const quizTimes = this.quizData.map(q => q.seconds);
    if (quizTimes.includes(Math.floor(seekingTime))) {
      // Pause the video if it's at a quiz time
      videoPlayer.pause();
      this.isPlaying = false;

      // Show the quiz overlay
      this.checkQuiz();

    }
  }
},

endSeek() {
  this.seeking = false;

  const videoPlayer = this.$refs.videoPlayer;
  if (videoPlayer) {
    // Ensure the video doesn't skip past the quiz times
    const quizTimes = this.quizData.map(q => q.seconds);
    if (quizTimes.includes(Math.floor(videoPlayer.currentTime))) {
      // Resume playing if the video is at a quiz time
      videoPlayer.play();
      this.isPlaying = true;
    }

    this.checkQuiz();
  }
},

    playPause() {
      const videoPlayer = this.$refs.videoPlayer;
      if (videoPlayer.paused) {
        videoPlayer.play();
        this.isPlaying = true;
      } else {
        videoPlayer.pause();
        this.isPlaying = false;
      }
    },
    toggleMute() {
      const videoPlayer = this.$refs.videoPlayer;
      videoPlayer.muted = !videoPlayer.muted;
      this.isMuted = videoPlayer.muted;
    },
  },
};
</script>

<style scoped>
.video-container {
  position: relative;
  width: 100%;
  height: auto;
  background-color: #1e1e1e;
  border-radius: 10px;
  overflow: hidden;
}

video {
  width: 100%;
  height: auto;
  display: block;
  border-radius: 10px;
}

.controls {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 5px;
  box-sizing: border-box;
}

.btn {
  background-color: transparent;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 50%;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.custom-seekbar {
  -webkit-appearance: none;
  width: 70%;
  height: 8px;
  background: #ff4081;
  border-radius: 5px;
  outline: none;
  cursor: pointer;
}

.custom-seekbar::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 15px;
  height: 15px;
  background: #fff;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s ease;
}

.custom-seekbar::-moz-range-thumb {
  width: 15px;
  height: 15px;
  background: #fff;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s ease;
}

.custom-seekbar:hover::-webkit-slider-thumb {
  background: #ff4081;
}

.custom-seekbar:hover::-moz-range-thumb {
  background: #ff4081;
}

.quiz-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 20px;
  border-radius: 15px;
  z-index: 1000;
  text-align: center;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.quiz-overlay.show {
  opacity: 1;
}

.quiz-content {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.quiz-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.quiz-option {
  padding: 10px 20px;
  background-color: #444;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.quiz-option:hover {
  background-color: #555;
}

.selected-option {
  background-color: #d012a0;
}
.selected-option:hover {
  background-color: #d012a0;
}

.correct-option {
  background-color: #4caf50;
}

.incorrect-option {
  background-color: #f44336;
}

.feedback {
  margin-top: 15px;
  font-size: 1.2em;
}

.correct {
  color: #4caf50;
}

.incorrect {
  color: #f44336;
}

.subtitle-btn {
  background-color: transparent;
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  border-radius: 50%;
  transition: background-color 0.3s ease;
}

.subtitle-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>
