<script lang="ts">
  import { CaretDownSolid, TrashBinSolid, EditSolid } from 'flowbite-svelte-icons';
  
  let quizzes = [
    { id: 1, question: "What is 2 + 2?", choices: ["3", "4", "5"], answer: "4", points: 5, expanded: false },
  ];

  let newQuestion = "";
  let newChoices: string[] = [];
  let newChoice = "";
  let newAnswer = "";
  let newPoints: number | null = null;
  let showModal = false;
  let isEditing = false;
  let editingQuizId: number | null = null;

  function addChoice() {
    if (newChoice.trim() && !newChoices.includes(newChoice)) {
      newChoices = [...newChoices, newChoice];
      newChoice = "";
    }
  }

  function removeChoice(index: number) {
    newChoices = newChoices.filter((_, i) => i !== index);
  }

  function addQuiz() {
    if (newQuestion.trim() && newAnswer.trim() && newChoices.length > 0 && newPoints !== null) {
      quizzes = [...quizzes, { id: Date.now(), question: newQuestion, choices: newChoices, answer: newAnswer, points: newPoints, expanded: false }];
      closeModal();
    }
  }

  function deleteQuiz(id: number) {
    quizzes = quizzes.filter(quiz => quiz.id !== id);
  }

  function toggleExpand(id: number) {
    quizzes = quizzes.map(quiz => quiz.id === id ? { ...quiz, expanded: !quiz.expanded } : quiz);
  }

  function editQuiz(quizId: number) {
    const quiz = quizzes.find(q => q.id === quizId);
    if (quiz) {
      isEditing = true;
      editingQuizId = quizId;
      newQuestion = quiz.question;
      newChoices = [...quiz.choices];
      newAnswer = quiz.answer;
      newPoints = quiz.points;
      showModal = true;
    }
  }

  function updateQuiz() {
    if (editingQuizId !== null && newPoints !== null) {
      quizzes = quizzes.map(quiz => 
        quiz.id === editingQuizId ? { ...quiz, question: newQuestion, choices: newChoices, answer: newAnswer, points: newPoints } : quiz
      );
      closeModal();
    }
  }

  function closeModal() {
    showModal = false;
    isEditing = false;
    editingQuizId = null;
    newQuestion = "";
    newChoices = [];
    newAnswer = "";
    newPoints = null;
  }
  function totalPoints() {
      return quizzes.reduce((sum, quiz) => sum + quiz.points, 0);
    }
</script>

<div class="text-gray-500 font-bold text-2xl pl-10">
  <h1>Quiz Management/Story 1</h1>
</div>
<div class="p-6 max-w-6xl mx-auto bg-white dark:bg-gray-900 shadow-lg rounded-lg">
  <div class="flex justify-between items-center mb-6">
    <button on:click={() => showModal = true} class="bg-lime-500 text-white px-5 py-2 rounded-lg hover:bg-lime-600 shadow ">+ Add Quiz</button>
  </div>

  <div class="overflow-x-auto mt-4">
    <table class="w-full bg-white text-sm rounded-lg shadow-md border border-gray-200">
      <thead class="bg-orange-500 text-white rounded-t-lg">
        <tr>
          <th class="p-4 text-left">Question</th>
          <th class="p-4 text-center">Points</th>
          <th class="p-4 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        {#each quizzes as quiz (quiz.id)}
          <tr 
            class="border-b border-gray-300 hover:bg-lime-100 transition cursor-pointer" 
            on:click={() => toggleExpand(quiz.id)}
          >
            <td class="p-4 text-gray-800">
              <span class="text-lg float-left"><CaretDownSolid/></span>
              {quiz.question}
            </td>
            <td class="p-4 text-center text-gray-800">{quiz.points}</td>
            <td class="p-4 text-center text-gray-700">
              <button on:click={(e) => { e.stopPropagation(); editQuiz(quiz.id); }} class="text-green-500 hover:text-green-700 mr-3"><EditSolid/></button>
              <button on:click={(e) => { e.stopPropagation(); deleteQuiz(quiz.id); }} class="text-red-500 hover:text-red-700"><TrashBinSolid/></button>
            </td>
          </tr>
          {#if quiz.expanded}
            <tr class="bg-lime-50">
              <td colspan="3" class="p-4 rounded-lg">
                <p class="text-gray-700"><strong>Choices:</strong>
                  {#each quiz.choices as choice}
                  <br>&nbsp;&nbsp;&nbsp;‣ {choice}
                  {/each}
                </p>
                <p class="text-green-600 font-semibold mt-2"><strong>Answer: </strong> {quiz.answer}</p>
                <p class="text-blue-600 font-semibold mt-2"><strong>Points: </strong> {quiz.points}</p>
              </td>
            </tr>
          {/if}
        {/each}
      </tbody>
    </table>
  </div>

  <div class="mt-4 text-right text-lg font-semibold text-gray-700">
    Total Points: {totalPoints()}
  </div>
</div>
