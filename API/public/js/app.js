
const videoPlayer = document.getElementById('videoPlayer');
const quizOverlay = document.getElementById('quizOverlay');
const questionText = document.getElementById('questionText');
const optionsContainer = document.getElementById('optionsContainer');
const submitAnswer = document.getElementById('submitAnswer');

const quizData = [
    {
        seconds: 10,
        question: "Which subject's project is Saif explaining?",
        options: ["Compiler", "OS", "System Analysis", "Embedded Systems"],
        correctAnswer: 0
    },
    {
        seconds: 17,
        question: "What was Saif's Academic Number?",
        options: ["200500", "260300", "200345", "200346"],
        correctAnswer: 3
    }
];

let currentQuizIndex = 0;

videoPlayer.addEventListener('timeupdate', () => {
    if(quizData[currentQuizIndex]){
    console.log(
    "Current Quiz Number: " + Number(+currentQuizIndex+1),
    '\nQuizzes left: '  + Number(quizData.length - currentQuizIndex),
    '\nCurrent Time: ' + videoPlayer.currentTime,
    '\nNext Question at: ' + quizData[currentQuizIndex].seconds
    )
    }
    if (currentQuizIndex < quizData.length && videoPlayer.currentTime >= quizData[currentQuizIndex].seconds) {
        videoPlayer.pause();
        showQuiz(quizData[currentQuizIndex]);
    }
});

function showQuiz(quiz) {
    questionText.textContent = quiz.question;
    optionsContainer.innerHTML = '';
    quiz.options.forEach((option, index) => {
        const optionLabel = document.createElement('label');
        optionLabel.classList.add('option-label');
        const optionInput = document.createElement('input');
        optionInput.type = 'radio';
        optionInput.name = 'option';
        optionInput.value = index;
        optionInput.classList.add('option-input');
        optionLabel.appendChild(optionInput);
        optionLabel.appendChild(document.createTextNode(option));
        optionsContainer.appendChild(optionLabel);
    });
    quizOverlay.classList.remove('hidden');
}

quizForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const selectedOption = document.querySelector('input[name="option"]:checked');
    if (selectedOption) {
        saveAnswer(quizData[currentQuizIndex], parseInt(selectedOption.value));
        quizOverlay.classList.add('hidden');
        videoPlayer.play();
        currentQuizIndex++;
    } else {
        alert("Please select an option!");
    }
});

function saveAnswer(quiz, answer) {
    fetch('/save-answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question: quiz.question,
            selectedOption: answer,
            correct: answer === quiz.correctAnswer
        })
    }).then(response => {
        if (response.ok) {
            console.log('Answer saved successfully!');
        }
    });
}
