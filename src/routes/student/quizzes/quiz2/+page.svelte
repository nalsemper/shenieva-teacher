<script>
  // @ts-nocheck
  import { onMount, onDestroy } from 'svelte';
  import { writable } from 'svelte/store';
  import { goto } from '$app/navigation';
  import QuizResultModal2 from '../../components/modals/quiz1/QuizResultModal2.svelte';
  import { initializeQuiz2Store, getQuiz2Store, resetQuiz2Store } from '$lib/store/quiz2_store';

  let quizData = [];
  let quiz2Store = null;
  let error = null;
  let showConfetti = false;
  let showModal = false;

  // Drag state
  const draggedAnswer = writable(null);

  // Fetch quiz questions on mount
  onMount(async () => {
    console.log('onMount running, quiz2Store exists:', !!quiz2Store);
    quiz2Store = getQuiz2Store();
    if (quiz2Store) {
      console.log('Reusing quiz2Store, attempts:', $quiz2Store?.attempts);
      return;
    }
    try {
      const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/student-story2/get_story2_quizzes.php');
      if (!response.ok) throw new Error(`Failed to fetch quiz data: ${response.statusText}`);
      const rawData = await response.json();
      // Parse id and points to numbers
      quizData = rawData.map(q => ({
        id: parseInt(q.id, 10),
        question: q.question,
        answer: q.answer,
        points: parseInt(q.points, 10)
      }));
      console.log('Fetched quiz data:', quizData);
      if (!Array.isArray(quizData) || quizData.length === 0) {
        throw new Error('No quiz data received');
      }
      quiz2Store = initializeQuiz2Store(quizData);
      console.log('Initialized quiz2Store, attempts:', $quiz2Store?.attempts);
    } catch (err) {
      error = err.message || 'Unknown error';
      console.error('Error fetching quiz:', err);
    }
  });

  onDestroy(() => {
    console.log('Quiz component destroyed');
  });

  // Derived state
  $: allAnswered = quiz2Store && $quiz2Store ? $quiz2Store.answers.every(answer => answer.assignedTo !== null) : false;
  $: maxAttemptsReached = quiz2Store && $quiz2Store ? $quiz2Store.attempts >= $quiz2Store.maxAttempts : false;
  $: maxScore = quizData.length;
  $: maxPoints = quizData.reduce((sum, q) => sum + (q.points || 1), 0);

  // Reactive logging for modal changes
  $: {
    console.log('showModal changed:', showModal, 'Attempts:', $quiz2Store?.attempts);
  }

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
    const hasAnswer = quiz2Store && $quiz2Store ? $quiz2Store.answers.some(answer => answer.assignedTo === question.id) : false;
    if ($draggedAnswer && !hasAnswer && quiz2Store && $quiz2Store && $draggedAnswer.text) {
      quiz2Store.updateAnswer($draggedAnswer.text, question.id);
    }
    $draggedAnswer = null;
  }

  function handleDropToAnswerBox(event) {
    event.preventDefault();
    if ($draggedAnswer && quiz2Store && $quiz2Store && $draggedAnswer.text) {
      quiz2Store.updateAnswer($draggedAnswer.text, null);
    }
    $draggedAnswer = null;
  }

  // Handle submit
  function handleSubmit() {
    if (maxAttemptsReached || !quiz2Store || !$quiz2Store) return;
    quiz2Store.submit(quizData);
    showConfetti = true;
    showModal = true;
    console.log('Submitted quiz, attempts:', $quiz2Store.attempts);
    setTimeout(() => (showConfetti = false), 3000);
  }

  // Handle modal actions
  function handleModalAction(event) {
    console.log('Modal action:', event.detail.action, 'Attempts before:', $quiz2Store?.attempts);
    showModal = false;
    if (event.detail.action === 'continue') {
      resetQuiz2Store(); // Clear store and storage
      goto('/student/game/trash_2', { invalidateAll: false, replaceState: true, noScroll: true });
    } else if (event.detail.action === 'retake' && !maxAttemptsReached && quiz2Store && $quiz2Store) {
      quiz2Store.resetForRetake();
      console.log('After retake, attempts:', $quiz2Store.attempts);
    }
  }
</script>

<div class="relative min-h-screen">
  {#if error}
    <div class="text-red-600 text-2xl font-bold text-center mt-10">
      Error: {error}
    </div>
  {:else if quizData.length === 0 || !quiz2Store}
    <div class="text-gray-600 text-2xl font-bold text-center mt-10">
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
      <div class="bg-gradient-to-r from-blue-100 to-purple-100 rounded-2xl shadow-2xl p-8 mb-10 w-full max-w-4xl text-center text-xl font-semibold text-gray-900 border-4 border-yellow-300 transform hover:scale-105 transition-transform">
        <p class="mb-3 text-2xl text-purple-700">Drag the sparkly answers to the matching questions!</p>
        <p class="text-lg">Want to change an answer? Drag it back to the answer box! (3 attempts max)</p>
      </div>

      <!-- Score and Points -->
      {#if quiz2Store && $quiz2Store}
        <div class="text-3xl font-bold text-blue-800 mb-8 animate-bounce">
          Score: {$quiz2Store.score}/{maxScore} | Points: {$quiz2Store.totalPoints}/{maxPoints} | Attempts: {$quiz2Store.attempts}/{$quiz2Store.maxAttempts}
        </div>
      {/if}

      <!-- Answer Box -->
      <div
        role="region"
        aria-label="Answer box"
        on:dragover={handleDragOver}
        on:drop={handleDropToAnswerBox}
        class="bg-gradient-to-b from-white to-gray-50 rounded-2xl shadow-xl p-8 mb-10 w-full max-w-4xl flex flex-wrap gap-6 justify-center border-4 border-dashed border-purple-400"
      >
        {#if quiz2Store && $quiz2Store}
          {#each $quiz2Store.answers as answer (answer.text)}
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
        {/if}
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
            {#if quiz2Store && $quiz2Store}
              {#each $quiz2Store.answers as answer}
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
            {/if}
          </div>
        {/each}
      </div>

      <!-- Submit Button -->
      {#if allAnswered && !maxAttemptsReached}
        <button
          on:click={handleSubmit}
          class="mt-8 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold text-2xl px-10 py-4 rounded-full shadow-lg hover:scale-105 hover:shadow-xl transition-all transform animate-pulse"
        >
          Submit Answers!
        </button>
      {/if}

      <!-- Confetti -->
      {#if showConfetti}
        <div class="fixed inset-0 pointer-events-none">
          {#each Array(100) as _, i}
            <div
              class="absolute w-3 h-3 rounded-full animate-confetti"
              style="
                background-color: hsl({Math.random() * 360}, 80%, 60%);
                left: {Math.random() * 100}%;
                top: {Math.random() * 100}%;
                animation-delay: {Math.random() * 1.5}s;
                animation-duration: {1.5 + Math.random() * 2}s;
              "
            ></div>
          {/each}
        </div>
      {/if}
    </div>
  {/if}

  <!-- Modal -->
  {#if showModal}
    <QuizResultModal2 quizData={quizData} quiz2Store={quiz2Store} on:action={handleModalAction} />
  {/if}
</div>

<style>
  .animate-confetti {
    animation: confetti-fall linear forwards;
  }

  @keyframes confetti-fall {
    0% {
      transform: translateY(0) rotate(0deg);
      opacity: 1;
    }
    100% {
      transform: translateY(100vh) rotate(720deg);
      opacity: 0;
    }
  }

  .delay-500 {
    animation-delay: 0.5s;
  }

  .delay-1000 {
    animation-delay: 1s;
  }
</style>