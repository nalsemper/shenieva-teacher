<script lang="ts">
  import { CaretDownSolid, TrashBinSolid, EditSolid } from 'flowbite-svelte-icons';
  
  interface Quiz {
    id: number;
    question: string;
    choices: string[];
    answer: string;
    points: number;
    expanded: boolean;
  }

  let story = "story1";
  let quizzes: Quiz[] = [];


  async function fetchQuizzes() {
  try {
    const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/fetch_quizzes.php?story=story1');
    const data = await response.json();
    console.log(data); 

    if (Array.isArray(data)) {
      quizzes = data.map(quiz => ({
  ...quiz,
  choices: Array.isArray(quiz.choices) ? quiz.choices : quiz.choices.split(','),
  expanded: false
}));

    } else {
      console.error('Invalid data format:', data);
    }
  } catch (error) {
    console.error("Failed to fetch quizzes:", error);
  }
}


  fetchQuizzes();  // Fetch quizzes on component mount

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

  async function addQuiz() {
  if (newQuestion.trim() && newAnswer.trim() && newChoices.length > 0 && newPoints !== null) {
    try {
      const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/add_quiz1.php?story=story1', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          question: newQuestion,
          choices: newChoices,
          answer: newAnswer,
          points: newPoints
        })
      });

      const result = await response.json();
      if (result.success) {
        fetchQuizzes(); // Refresh quiz list
        closeModal();
      } else {
        console.error('Failed to add quiz:', result.error);
      }
    } catch (error) {
      console.error('Error adding quiz:', error);
    }
  }
}


async function deleteQuiz(id: number) {
    if (confirm("Are you sure you want to delete this quiz?")) {
        try {
            const response = await fetch(`http://localhost/shenieva-teacher/src/lib/api/delete_quiz.php?story=story1`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) // Send ID in the request body
            });

            const result = await response.json();

            if (result.success) {
                quizzes = quizzes.filter(quiz => quiz.id !== id); // Remove from UI only if successful
            } else {
                console.error('Failed to delete quiz:', result.error);
            }
        } catch (error) {
            console.error('Error deleting quiz:', error);
        }
    }
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
        newChoices = quiz.choices;
        newAnswer = quiz.answer;
        newPoints = quiz.points;
        showModal = true;
    }
}

async function updateQuiz() {
    if (editingQuizId !== null && newPoints !== null) {
        try {
            const formattedChoices = Array.isArray(newChoices) ? newChoices : newChoices.split(',');

            const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/edit_quiz.php?story=story1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: editingQuizId,
                    question: newQuestion,
                    choices: formattedChoices,  // Save as JSON string
                    answer: newAnswer,
                    points: newPoints
                })
            });
            
            const result = await response.json();
            if (result.success) {
                fetchQuizzes();  
                closeModal();
            } else {
                console.error('Failed to update quiz:', result.error);
            }
        } catch (error) {
            console.error('Error updating quiz:', error);
        }
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
  <h1>Quiz Management/{story}</h1>
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
                  {#each (quiz.choices) as choice}
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
