<script lang="ts">
  import { TrashBinSolid, EditSolid } from 'flowbite-svelte-icons';
  
    let quizzes = [
      { id: 1, question: "What is 2 + 2?", expanded: false },
    ];
  
    let newQuestion = "";
    let showModal = false;
    let isEditing = false;
    let editingQuizId: number | null = null;
  
    function addQuiz() {
      if (newQuestion.trim()) {
        quizzes = [...quizzes, { id: Date.now(), question: newQuestion, expanded: false }];
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
        showModal = true;
      }
    }
  
    function updateQuiz() {
      if (editingQuizId !== null) {
        quizzes = quizzes.map(quiz => 
          quiz.id === editingQuizId ? { ...quiz, question: newQuestion } : quiz
        );
        closeModal();
      }
    }
  
    function closeModal() {
      showModal = false;
      isEditing = false;
      editingQuizId = null;
      newQuestion = "";
    }
  </script>
  
  <h1 class="text-2xl font-bold text-gray-500 dark:text-white pb-3">Quiz Management/Story 3</h1>
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
                {quiz.question}
              </td>
              <td class="p-4 text-center text-gray-700">
                <button on:click={(e) => { e.stopPropagation(); editQuiz(quiz.id); }} class="text-green-500 hover:text-green-700 mr-3"><EditSolid/></button>
                <button on:click={(e) => { e.stopPropagation(); deleteQuiz(quiz.id); }} class="text-red-500 hover:text-red-700"><TrashBinSolid/></button>
              </td>
            </tr>
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
    
        <input bind:value={newQuestion} placeholder="Enter Question" class="w-full p-3 border border-orange-300 rounded-lg mb-4 focus:ring-2 focus:ring-orange-400 focus:outline-none" />
    
        <button on:click={isEditing ? updateQuiz : addQuiz} class="bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition w-full">{isEditing ? 'Update Quiz' : 'Add Quiz'}</button>
      </div>
    </div>
    {/if}
  </div>