<script lang="ts">
  import { onMount } from 'svelte';
  import { writable } from 'svelte/store';
  import { goto } from '$app/navigation';
  import QuizResultModal2 from '../../components/modals/quiz1/QuizResultModal2.svelte';
  import CongratsModal from '../../components/modals/quiz1/CongratsModal.svelte';

  // Quiz state
  let quizData = [];
  let error = null;
  const quizStore = writable({
    answers: []
  });
  let showResultModal = false;
  let showCongratsModal = false;
  let quizResults = [];
  let earnedPoints = 0;
  let attemptCount = 0;
  let maxAttempts = 3;

  // Assume student_id is available (e.g., from session or context)
  let student_id = 1; // REPLACE WITH ACTUAL STUDENT ID RETRIEVAL LOGIC

  // Drag state
  const draggedAnswer = writable(null);

  onMount(async () => {
    console.log('Quiz component mounted');
    try {
      // Fetch quiz data
      const quizResponse = await fetch('http://localhost:5173/api/student-story2/get_story2_quizzes.php', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        }
      });

      if (!quizResponse.ok) {
        throw new Error(`Failed to fetch quiz data: ${quizResponse.statusText}`);
      }

      const data = await quizResponse.json();
      
      if (data.error) {
        throw new Error(data.error);
      }

      // Store original quiz data, ensuring points is a number
      quizData = data.map(q => ({ ...q, points: Number(q.points) }));
      
      // Create randomized answers
      const randomizedAnswers = data
        .map(q => ({ text: q.answer, assignedTo: null, originalQuestionId: q.id, points: Number(q.points) }))
        .sort(() => Math.random() - 0.5);
      
      quizStore.set({
        answers: randomizedAnswers
      });

      // Fetch attempt count for the student
      const attemptResponse = await fetch(`http://localhost:5173/api/student-story2/get_student_attempts.php?student_id=${student_id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        }
      });

      if (!attemptResponse.ok) {
        throw new Error(`Failed to fetch attempt count: ${attemptResponse.statusText}`);
      }

      const attemptData = await attemptResponse.json();
      if (attemptData.error) {
        throw new Error(attemptData.error);
      }

      attemptCount = attemptData.attempts || 0;

    } catch (err) {
      error = err.message;
      console.error('Mount error:', err);
    }

    return () => {
      console.log('Quiz component destroyed');
    };
  });

  // Derived state
  $: allAnswered = $quizStore.answers.every(answer => answer.assignedTo !== null);

  // Drag and drop handlers
  function handleDragStart(event, answer) {
    $draggedAnswer = answer;
    if (event.dataTransfer && answer) {
      event.dataTransfer.setData('text/plain', answer.text);
    }
  }

  function handleDragOver(event) {
    event.preventDefault();
  }

  function handleDrop(event, question) {
    event.preventDefault();
    const hasAnswer = $quizStore.answers.some(answer => answer.assignedTo === question.id);
    if ($draggedAnswer && !hasAnswer && $draggedAnswer.text) {
      quizStore.update(store => {
        const updatedAnswers = store.answers.map(ans =>
          ans.text === $draggedAnswer.text ? { ...ans, assignedTo: question.id } : ans
        );
        return { ...store, answers: updatedAnswers };
      });
    }
    $draggedAnswer = null;
  }

  function handleDropToAnswerBox(event) {
    event.preventDefault();
    if ($draggedAnswer && $draggedAnswer.text) {
      quizStore.update(store => {
        const updatedAnswers = store.answers.map(ans =>
          ans.text === $draggedAnswer.text ? { ...ans, assignedTo: null } : ans
        );
        return { ...store, answers: updatedAnswers };
      });
    }
    $draggedAnswer = null;
  }

  async function saveQuizResults(isFinal: boolean) {
    try {
      const response = await fetch('http://localhost:5173/api/student-story2/save_quiz_results.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          student_id,
          attempt: attemptCount + 1,
          results: quizResults,
          is_final: isFinal ? 1 : 0
        })
      });

      const data = await response.json();
      if (!response.ok || data.error) {
        throw new Error(data.error || `Failed to save quiz results: ${response.status}`);
      }
      console.log('Quiz results saved:', data);
    } catch (err) {
      console.error('Error saving quiz results:', err.message);
      error = 'Failed to save quiz results. Please try again.';
      throw err;
    }
  }

  async function saveRibbons(ribbons: number) {
    try {
      console.log(`Attempting to save ${ribbons} ribbons for student_id: ${student_id}`);
      const response = await fetch('http://localhost:5173/api/student-story2/save_student_ribbons.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          student_id,
          ribbons
        })
      });

      const data = await response.json();
      if (!response.ok || data.error) {
        throw new Error(data.error || `Failed to save ribbons: ${response.status}`);
      }
      console.log('Ribbons saved:', data);
    } catch (err) {
      console.error('Error saving ribbons:', err.message);
      error = 'Error saving ribbons. Please try again.';
      throw err;
    }
  }

  function handleSubmit() {
    try {
      quizResults = $quizStore.answers.map(answer => ({
        question: quizData.find(q => q.id === answer.assignedTo)?.question || 'Not assigned',
        answer: answer.text,
        isCorrect: answer.assignedTo === answer.originalQuestionId,
        points: Number(quizData.find(q => q.id === answer.originalQuestionId)?.points || 0)
      }));
      earnedPoints = quizResults.reduce((sum, r) => sum + (r.isCorrect ? r.points : 0), 0);
      console.log('Calculated earnedPoints:', earnedPoints);
      showResultModal = true;
    } catch (err) {
      console.error('Error in handleSubmit:', err);
      error = 'Error processing quiz results.';
    }
  }

  async function handleRetake() {
    try {
      if (attemptCount < maxAttempts) {
        await saveQuizResults(false);
        attemptCount += 1;
        // Reset quiz
        const randomizedAnswers = quizData
          .map(q => ({ text: q.answer, assignedTo: null, originalQuestionId: q.id, points: Number(q.points) }))
          .sort(() => Math.random() - 0.5);
        
        quizStore.set({
          answers: randomizedAnswers
        });
        showResultModal = false;
      }
    } catch (err) {
      console.error('Error during retake:', err);
      error = 'Error resetting quiz.';
    }
  }

  async function handleFinalSubmit() {
    try {
      console.log('Final submit triggered');
      await saveQuizResults(true);
      await saveRibbons(earnedPoints);
      showResultModal = false;
      showCongratsModal = true;
    } catch (err) {
      console.error('Error in handleFinalSubmit:', err);
      error = 'Error finalizing submission. Please try again.';
    }
  }

  function handleProceed() {
    try {
      console.log('Proceeding to trash_2 page');
      showCongratsModal = false;
      goto('/student/game/trash_2');
    } catch (err) {
      console.error('Error navigating:', err);
      error = 'Error navigating to next page.';
    }
  }
</script>

<div class="relative min-h-screen">
  {#if error}
    <div class="text-red-600 text-center text-2xl font-bold mt-4">
      {error}
    </div>
  {/if}
  {#if quizData.length === 0 && !error}
    <div class="text-gray-600 text-center text-2xl font-bold mt-10">
      Loading quiz...
    </div>
  {:else}
    <div class="h-screen w-screen bg-gradient-to-br from-indigo-200 via-purple-200 to-pink-200 p-8 flex flex-col items-center overflow-auto relative">
      <!-- Animated background elements -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute w-40 h-40 bg-yellow-300 opacity-20 rounded-full top-10 left-10 animate-pulse"></div>
        <div class="absolute w-60 h-60 bg-pink-300 opacity-20 rounded-full bottom-20 right-20 animate-pulse delay-1000"></div>
        <div class="absolute w-20 h-20 bg-blue-300 opacity-20 rounded-full top-1/2 left-1/3 animate-pulse delay-500"></div>
      </div>

      <!-- Header -->
      <h1 class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-6 animate-pulse">
        Super Fun Quiz Adventure!
      </h1>

      <!-- Instructions -->
      <div class="bg-gradient-to-r from-blue-100▬to-purple-100 rounded-2xl shadow-2xl p-8 mb-10 w-full max-w-4xl text-center text-xl font-semibold text-gray-900 border-4 border-yellow-300 transform hover:scale-105 transition-transform">
        <p class="mb-3 text-2xl text-purple-700">Drag the sparkly answers to the matching questions!</p>
        <p class="text-lg">Want to change an answer? Drag it back to the answer box!</p>
      </div>

      <!-- Answer Box -->
      <div
        role="region"
        aria-label="Answer box"
        on:dragover={handleDragOver}
        on:drop={handleDropToAnswerBox}
        class="bg-gradient-to-b from-white to-gray-50 rounded-2xl shadow-xl p-8 mb-10 w-full max-w-4xl flex flex-wrap gap-6 justify-center border-4 border-dashed border-purple-400"
      >
        {#each $quizStore.answers as answer (answer.text)}
          {#if !answer.assignedTo}
            <div
              role="button"
              aria-label={`Drag answer: ${answer.text}`}
              tabindex="0"
              draggable="true"
              on:dragstart={event => handleDragStart(event, answer)}
              class="bg-gradient-to-r from-yellow-400 to-orange-400 text-purple-900 font-bold px-6 py-3 rounded-full cursor-move hover:scale-110 hover:shadow-lg transition-all transform"
            >
              {answer.text}
            </div>
          {/if}
        {/each}
      </div>

      <!-- Questions -->
      <div class="grid gap-8 w-full max-w-4xl flex-1">
        {#each quizData as question (question.id)}
          <div
            role="region"
            aria-label={`Question: ${question.question}`}
            on:dragover={handleDragOver}
            on:drop={event => handleDrop(event, question)}
            class="bg-white rounded-2xl shadow-lg p-8 text-xl font-semibold text-gray-900 flex items-center justify-between hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all transform hover:-translate-y-1 border-2 border-purple-200"
          >
            <span class="flex-1">{question.question}</span>
            {#each $quizStore.answers as answer}
              {#if answer.assignedTo === question.id}
                <div
                  role="button"
                  aria-label={`Assigned answer: ${answer.text}`}
                  tabindex="0"
                  draggable="true"
                  on:dragstart={event => handleDragStart(event, answer)}
                  class="bg-gradient-to-r from-yellow-400 to-orange-400 text-purple-900 font-bold px-6 py-3 rounded-full cursor-move hover:scale-110 transition-transform"
                >
                  {answer.text}
                </div>
              {/if}
            {/each}
          </div>
        {/each}
      </div>

      <!-- Submit Button -->
      {#if allAnswered}
        <button
          on:click={handleSubmit}
          class="mt-8 bg-gradient-to-r from-green-400 to-blue-500 text-white font-bold px-8 py-4 rounded-full hover:scale-105 transition-transform shadow-lg"
        >
          Submit Quiz
        </button>
      {/if}
    </div>

    {#if showResultModal}
      <QuizResultModal2
        results={quizResults}
        attemptCount={attemptCount}
        maxAttempts={maxAttempts}
        on:retake={handleRetake}
        on:submit={handleFinalSubmit}
      />
    {/if}

    {#if showCongratsModal}
      <CongratsModal
        ribbons={earnedPoints}
        on:proceed={handleProceed}
      />
    {/if}
  {/if}
</div>