<script lang="ts">
  import { TrashBinSolid, EditSolid } from "flowbite-svelte-icons";

  interface Quiz {
    id: number;
    question: string;
    expanded: boolean;
  }

  let story = "story3";
  let quizzes: Quiz[] = [];

  async function fetchQuizzes() {
    try {
      const response = await fetch(
        "http://localhost/shenieva-teacher/src/lib/api/fetch_quizzes.php?story=story3",
      );
      const data = await response.json();
      console.log(data);

      if (Array.isArray(data)) {
        quizzes = data.map((quiz) => ({
          ...quiz,
          expanded: false,
        }));
      } else {
        console.error("Invalid data format:", data);
      }
    } catch (error) {
      console.error("Failed to fetch quizzes:", error);
    }
  }

  fetchQuizzes(); // Fetch quizzes on component mount

  let newQuestion = "";
  let showModal = false;
  let isEditing = false;
  let editingQuizId: number | null = null;

  async function addQuiz() {
    if (newQuestion.trim()) {
      try {
        const response = await fetch(
          "http://localhost/shenieva-teacher/src/lib/api/add_quiz1.php?story=story3",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ question: newQuestion }),
          },
        );

        const result = await response.json();
        if (result.success) {
          fetchQuizzes(); // Refresh quiz list
          closeModal();
        } else {
          console.error("Failed to add quiz:", result.error);
        }
      } catch (error) {
        console.error("Error adding quiz:", error);
      }
    }
  }

  async function deleteQuiz(id: number) {
    if (confirm("Are you sure you want to delete this quiz?")) {
      try {
        const response = await fetch(
          `http://localhost/shenieva-teacher/src/lib/api/delete_quiz.php?story=story3`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id }),
          },
        );

        const result = await response.json();
        if (result.success) {
          quizzes = quizzes.filter((quiz) => quiz.id !== id);
        } else {
          console.error("Failed to delete quiz:", result.error);
        }
      } catch (error) {
        console.error("Error deleting quiz:", error);
      }
    }
  }

  function editQuiz(quizId: number) {
    const quiz = quizzes.find((q) => q.id === quizId);
    if (quiz) {
      isEditing = true;
      editingQuizId = quizId;
      newQuestion = quiz.question;
      showModal = true;
    }
  }

  async function updateQuiz() {
    if (editingQuizId !== null) {
      try {
        const response = await fetch(
          "http://localhost/shenieva-teacher/src/lib/api/edit_quiz.php?story=story3",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: editingQuizId, question: newQuestion }),
          },
        );

        const result = await response.json();
        if (result.success) {
          fetchQuizzes();
          closeModal();
        } else {
          console.error("Failed to update quiz:", result.error);
        }
      } catch (error) {
        console.error("Error updating quiz:", error);
      }
    }
  }

  function closeModal() {
    showModal = false;
    isEditing = false;
    editingQuizId = null;
    newQuestion = "";
  }
</script>

<div class="text-gray-500 font-bold text-2xl pl-10">
  <h1>Quiz Management/{story}</h1>
</div>
<div
  class="p-6 max-w-6xl mx-auto bg-white dark:bg-gray-900 shadow-lg rounded-lg"
>
  <div class="flex justify-between items-center mb-6">
    <button
      on:click={() => (showModal = true)}
      class="bg-lime-500 text-white px-5 py-2 rounded-lg hover:bg-lime-600 shadow"
      >+ Add Quiz</button
    >
  </div>

  <div class="overflow-x-auto mt-4">
    <table
      class="w-full bg-white text-sm rounded-lg shadow-md border border-gray-200"
    >
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
          >
            <td class="p-4 text-gray-800">{quiz.question}</td>
            <td class="p-4 text-center text-gray-700">
              <button
                on:click={(e) => {
                  e.stopPropagation();
                  editQuiz(quiz.id);
                }}
                class="text-green-500 hover:text-green-700 mr-3"
                ><EditSolid /></button
              >
              <button
                on:click={(e) => {
                  e.stopPropagation();
                  deleteQuiz(quiz.id);
                }}
                class="text-red-500 hover:text-red-700"
                ><TrashBinSolid /></button
              >
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>

  {#if showModal}
    <div class="fixed inset-0 flex items-center justify-center bg-black/50">
      <div
        class="bg-white p-6 rounded-2xl shadow-2xl w-[700px] relative border-t-4 border-orange-500"
      >
        <button
          on:click={closeModal}
          class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl"
        >
          ✖
        </button>

        <h2 class="text-2xl font-bold text-orange-600 text-center mb-4">
          {isEditing ? "Edit Quiz" : "Add New Quiz"}
        </h2>

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
                 {newQuestion
              ? 'top-[-10px] px-2'
              : 'top-3 text-gray-400 text-lg peer-placeholder-shown:top-3 peer-placeholder-shown:text-lg'}
                 peer-focus:top-[-10px] peer-focus:text-orange-600 peer-focus:text-sm peer-focus:px-2"
          >
            Enter Question
          </label>
        </div>

        <button
          on:click={isEditing ? updateQuiz : addQuiz}
          class="bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition w-full"
        >
          {isEditing ? "Update Quiz" : "Add Quiz"}
        </button>
      </div>
    </div>
  {/if}
</div>
