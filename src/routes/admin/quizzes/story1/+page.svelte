<script lang="ts">
  import { CaretDownSolid, TrashBinSolid, EditSolid } from 'flowbite-svelte-icons';
  
    let quizzes = [
      { id: 1, question: "What is 2 + 2?", choices: ["3", "4", "5"], answer: "4", expanded: false },
    ];
  
    let newQuestion = "";
    let newChoices: string[] = [];
    let newChoice = "";
    let newAnswer = "";
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
      if (newQuestion.trim() && newAnswer.trim() && newChoices.length > 0) {
        quizzes = [...quizzes, { id: Date.now(), question: newQuestion, choices: newChoices, answer: newAnswer, expanded: false }];
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
        showModal = true;
      }
    }
  
    function updateQuiz() {
      if (editingQuizId !== null) {
        quizzes = quizzes.map(quiz => 
          quiz.id === editingQuizId ? { ...quiz, question: newQuestion, choices: newChoices, answer: newAnswer } : quiz
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
    }
  </script>
  
  <h1 class="text-2xl font-bold text-gray-500 dark:text-white pb-3">Quiz Management/Story 1</h1>
  <div class="p-6 max-w-6xl mx-auto bg-white dark:bg-gray-900 shadow-lg rounded-lg">
    <div class="flex justify-between items-center mb-6">
      <button on:click={() => showModal = true} class="bg-lime-500 text-white px-5 py-2 rounded-lg hover:bg-lime-600 shadow ">+ Add Quiz</button>
    </div>
  
    <div class="overflow-x-auto mt-4">
      <table class="w-full bg-white text-sm rounded-lg shadow-md border border-gray-200">
        <thead class="bg-orange-500 text-white rounded-t-lg">
          <tr>
            <th class="p-4 text-left">Question</th>
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
              <td class="p-4 text-center text-gray-700">
                <button on:click={(e) => { e.stopPropagation(); editQuiz(quiz.id); }} class="text-green-500 hover:text-green-700 mr-3"><EditSolid/></button>
                <button on:click={(e) => { e.stopPropagation(); deleteQuiz(quiz.id); }} class="text-red-500 hover:text-red-700"><TrashBinSolid/></button>
              </td>
            </tr>
            {#if quiz.expanded}
              <tr class="bg-lime-50">
                <td colspan="2" class="p-4 rounded-lg">
                  <p class="text-gray-700"><strong>Choices:</strong>
                    {#each quiz.choices as choice}
                    <br>&nbsp;&nbsp;&nbsp;‣ {choice}
                    {/each}
                  </p>
                  <p class="text-green-600 font-semibold mt-2"><strong>Answer: </strong> {quiz.answer}</p>
                </td>
              </tr>
            {/if}
          {/each}
        </tbody>
      </table>
    </div>
  
    {#if showModal}
    <div class="fixed inset-0 flex items-center justify-center bg-black/50">
      <div class="bg-white p-6 rounded-2xl shadow-2xl w-[500px] relative border-t-4 border-orange-500">
        <button on:click={closeModal} class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl">
          ✖
        </button>
    
        <h2 class="text-2xl font-bold text-orange-600 text-center mb-4">{isEditing ? 'Edit Quiz' : 'Add New Quiz'}</h2>
    
        <input 
          bind:value={newQuestion} 
          placeholder="Enter Question" 
          class="w-full p-3 border border-orange-300 rounded-lg mb-4 focus:ring-2 focus:ring-orange-400 focus:outline-none"
        />
    
        <div class="mb-4">
          <div class="flex space-x-2 mb-3">
            <input 
              bind:value={newChoice} 
              placeholder="Add choice" 
              class="flex-1 p-3 border border-lime-300 rounded-lg focus:ring-2 focus:ring-lime-400 focus:outline-none"
            />
            <button on:click={addChoice} class="bg-lime-500 text-white px-5 py-2 rounded-lg hover:bg-lime-600 transition">+</button>
          </div>
    
          {#each newChoices as choice, index}
            <div class="flex justify-between items-center bg-gray-100 p-2 rounded mb-2">
              <span>{choice}</span>
              <button on:click={() => removeChoice(index)} class="text-red-500 hover:text-red-700">✖</button>
            </div>
          {/each}
    
          <select bind:value={newAnswer} class="w-full p-3 border border-orange-300 rounded-lg mb-4">
            <option value="" disabled>Select Answer</option>
            {#each newChoices as choice}
              <option value={choice}>{choice}</option>
            {/each}
          </select>
    
        </div>
    
        <button on:click={isEditing ? updateQuiz : addQuiz} class="bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition w-full">
          {isEditing ? 'Update Quiz' : 'Add Quiz'}
        </button>
      </div>
    </div>
    {/if}
  </div>
