<script lang="ts">
  import { CaretDownSolid, TrashBinSolid, EditSolid } from 'flowbite-svelte-icons';
  
    let quizzes = [
      { id: 1, question: "What is 2 + 2?", answer: "4", points: 5, expanded: false },
    ];
  
    let newQuestion = "";
    let newAnswer = "";
    let newPoints: number | null = null;
    let showModal = false;
    let isEditing = false;
    let editingQuizId: number | null = null;
  
    function addQuiz() {
      if (newQuestion.trim() && newAnswer.trim() && newPoints !== null) {
        quizzes = [...quizzes, { id: Date.now(), question: newQuestion, answer: newAnswer, points: newPoints, expanded: false }];
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
        newAnswer = quiz.answer;
        newPoints = quiz.points;
        showModal = true;
      }
    }
  
    function updateQuiz() {
      if (editingQuizId !== null && newPoints !== null) {
        quizzes = quizzes.map(quiz => 
          quiz.id === editingQuizId ? { ...quiz, question: newQuestion, answer: newAnswer, points: newPoints } : quiz
        );
        closeModal();
      }
    }
  
    function closeModal() {
      showModal = false;
      isEditing = false;
      editingQuizId = null;
      newQuestion = "";
      newAnswer = "";
      newPoints = null;
    }
  
    function totalPoints() {
      return quizzes.reduce((sum, quiz) => sum + quiz.points, 0);
    }
  </script>
  
  <div class="text-gray-500 font-bold text-2xl pl-10">
    <h1>Quiz Management/Story 2</h1>
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
                  <p class="text-green-600 font-semibold mt-2">Answer: {quiz.answer}</p>
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
  
    {#if showModal}
    <div class="fixed inset-0 flex items-center justify-center bg-black/50">
      <div class="bg-white p-6 rounded-2xl shadow-2xl w-[700px] relative border-t-4 border-orange-500">
        <button on:click={closeModal} class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl">
          ✖
        </button>
    
        <h2 class="text-2xl font-bold text-orange-600 text-center mb-4">{isEditing ? 'Edit Quiz' : 'Add New Quiz'}</h2>
    
        <!-- Question Input -->
        <div class="relative w-full mb-4">
          <input 
            bind:value={newQuestion} 
            id="question" 
            placeholder=" " 
            class="peer w-full p-3 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
          />
          <label 
            for="question" 
            class="absolute left-3 bg-white px-2 text-sm text-orange-600 transition-all 
                   {newQuestion ? 'top-[-10px] px-2' : 'top-3 text-gray-400 text-lg peer-placeholder-shown:top-3 peer-placeholder-shown:text-lg'}
                   peer-focus:top-[-10px] peer-focus:text-orange-600 peer-focus:text-sm peer-focus:px-2"
          >
            Enter Question
          </label>
        </div>
    
        <!-- Answer Input -->
        <div class="relative w-full mb-4">
          <input 
            bind:value={newAnswer} 
            id="answer" 
            placeholder=" " 
            class="peer w-full p-3 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
          />
          <label 
            for="answer" 
            class="absolute left-3 bg-white px-2 text-sm text-orange-600 transition-all 
                   {newAnswer ? 'top-[-10px] px-2' : 'top-3 text-gray-400 text-lg peer-placeholder-shown:top-3 peer-placeholder-shown:text-lg'}
                   peer-focus:top-[-10px] peer-focus:text-orange-600 peer-focus:text-sm peer-focus:px-2"
          >
            Enter Answer
          </label>
        </div>
    
        <!-- Points Input -->
        <div class="relative w-full mb-4">
          <input 
            type="number" 
            bind:value={newPoints} 
            id="points" 
            min="5" max="100" step="5" 
            placeholder=" " 
            class="peer w-full p-3 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
          />
          <label 
            for="points" 
            class="absolute left-3 bg-white px-2 text-sm text-orange-600 transition-all 
                   {newPoints ? 'top-[-10px] px-2' : 'top-3 text-gray-400 text-lg peer-placeholder-shown:top-3 peer-placeholder-shown:text-lg'}
                   peer-focus:top-[-10px] peer-focus:text-orange-600 peer-focus:text-sm peer-focus:px-2"
          >
            Enter Points
          </label>
        </div>
    
        <button on:click={isEditing ? updateQuiz : addQuiz} class="bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition w-full">
          {isEditing ? 'Update Quiz' : 'Add Quiz'}
        </button>
      </div>
    </div>
    
    {/if}
  </div>
