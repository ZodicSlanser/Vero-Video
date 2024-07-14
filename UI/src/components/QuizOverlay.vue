<template>
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
            <button class="btn submit-btn" type="submit">Submit</button>
        </form>
    </div>
</template>

<script>
export default {
    props: {
        currentQuestion: Object,
        showQuizOverlay: Boolean,
    },
    data() {
        return {
            selectedOption: null,
            userAnswer: null,
            correctAnswer: null,
        };
    },
    methods: {
        async submitAnswer() {
            const question = this.currentQuestion;
            const answer = this.selectedOption;
            this.userAnswer = answer;
            this.correctAnswer = question.correct_answer;

            try {
                await this.$emit('submit-answer', {
                    user_id: 1,
                    quiz_question_id: question.id,
                    selected_option: answer,
                    correct: answer === question.correct_answer,
                });
                this.$emit('hide-quiz-overlay');
                this.selectedOption = null;
            } catch (error) {
                console.error('Error saving answer:', error);
            }
        },
        getOptionClass(index) {
            if (this.userAnswer === null) {
                return { 'selected-option': this.selectedOption === index };
            }
            if (index === this.correctAnswer) {
                return 'correct-option';
            }
            if (index === this.userAnswer) {
                return 'incorrect-option';
            }
            return '';
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

.quiz-option {
    margin: 5px 0;
    padding: 8px;
}

.quiz-option label {
    display: flex;
    align-items: center;
    padding-left: 20px;
}

.submit-btn {
    margin-top: 10px;
    width: 100%;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 20px;
    padding: 10px;
    cursor: pointer;
}

.submit-btn:hover {
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
    .quiz-overlay {
        width: 50%;
        padding: 15px;
    }

    .quiz-option {
        margin: 3px 0;
        padding: 6px;
    }

    .submit-btn {
        padding: 8px;
    }
}

@media (max-width: 480px) {
    .quiz-overlay {
        width: 50%;
        padding: 10px;
    }

    .quiz-option {
        margin: 2px 0;
        padding: 5px;
    }

    .submit-btn {
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

    .submit-btn {
        padding: 4px;
    }
}
</style>
