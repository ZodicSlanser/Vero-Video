<template>
  <div class="container">
    <video
      v-if="renderVideo"
      ref="videoPlayer"
      width="640"
      height="360"
      controls
      @timeupdate="checkQuiz"
    >
      <source :src="videoPath" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
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
    };
  },
  mounted() {
    this.loadVideoData();
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
    checkQuiz() {
      const videoPlayer = this.$refs.videoPlayer;
      if (
        this.currentQuizIndex < this.quizData.length &&
        videoPlayer.currentTime >= this.quizData[this.currentQuizIndex].seconds
      ) {
        videoPlayer.pause();
        this.showQuizOverlay = true;
        this.currentQuestion = this.quizData[this.currentQuizIndex];
        this.correctAnswer = this.currentQuestion.correct_answer;
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
        await axios.post(serverURL + "/api/user-answers", {
          user_id: 1, // Example user ID
          quiz_question_id: question.id,
          selected_option: answer,
          correct: answer === question.correct_answer,
        });
        this.isSubmitted = true;
      } catch (error) {
        console.error("Error saving answer:", error);
      }
    },
    closeOverlay() {
      this.showQuizOverlay = false;
      this.currentQuizIndex++;
      this.selectedOption = null;
      this.$refs.videoPlayer.play();
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
  },
};
</script>

<style scoped>
.quiz-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255, 255, 255, 0.906);
  padding: 20px;
  border: 1px solid #ccc;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  width: 50%;
  text-align: center;
  border-radius: 15px;
}
.container {
  width: 70%;
}

video {
  width: 100%;
  height: auto;
  display: block;
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

.submit-btn, .done-btn {
  margin-top: 10px;
  width: 100%;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 10px;
  cursor: pointer;
}

.done-btn {
  background-color: #28a745;
}

.submit-btn:hover, .done-btn:hover {
  background-color: #0056b3;
}

.selected-option {
  background-color: #f0f0f0ab;
}

.correct-option {
  background-color: #28a745;
  color: white;
}

.incorrect-option {
  background-color: #dc3545;
  color: white;
}

@media (max-width: 768px) {
  .container {
    width: 80%;
  }
  .quiz-overlay {
    width: 50%;
    padding: 15px;
  }

  .quiz-option {
    margin: 3px 0;
    padding: 6px;
  }

  .submit-btn, .done-btn {
    padding: 8px;
  }
}
@media (max-width: 480px) {
  .container {
    width: 90%;
  }
  .quiz-overlay {
    width: 50%;
    padding: 10px;
  }

  .quiz-option {
    margin: 2px 0;
    padding: 5px;
  }

  .submit-btn, .done-btn {
    padding: 6px;
  }
}
@media (max-width: 350px) {
  .quiz-overlay {
    width: 50%;
    padding: 5px;
  }

  .quiz-option {
    margin: 1px 0;
    padding: 2px;
  }

  .submit-btn, .done-btn {
    padding: 4px;
  }
}
</style>
