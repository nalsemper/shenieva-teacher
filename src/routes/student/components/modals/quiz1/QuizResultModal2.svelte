<script>
    import { createEventDispatcher } from 'svelte';
    import { quiz2Store } from '$lib/store/quiz2_taking';
  
    export let quizData;
    const dispatch = createEventDispatcher();
    $: isPerfect = $quiz2Store.score === quizData.length; // Perfect if all questions correct
    $: maxAttempts = $quiz2Store.attempts >= $quiz2Store.maxAttempts;
    $: maxScore = quizData.length; // Total number of questions
    $: maxPoints = quizData.reduce((sum, q) => sum + (q.points || 1), 0); // Total possible points
  
    // Map answers to questions for display
    $: results = quizData.map(question => {
      const assignedAnswer = $quiz2Store.answers.find(answer => answer.assignedTo === question.id);
      const isCorrect = assignedAnswer && assignedAnswer.text === question.answer;
      return {
        question: question.question,
        answer: assignedAnswer ? assignedAnswer.text : 'No answer',
        isCorrect,
        points: question.points || 1
      };
    });
  
    function handleRetake() {
      dispatch('action', { action: 'retake' });
    }
  
    function handleContinue() {
      dispatch('action', { action: 'continue' });
    }
  </script>
  
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-2xl p-8 w-full max-w-lg text-center transform scale-100 transition-transform">
      <!-- Header -->
      {#if isPerfect}
        <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-4">
          Perfect Score! 🎉
        </h2>
        <p class="text-xl text-gray-800 mb-6">Wow, you got all {maxScore} questions correct for {maxPoints} points! You're a quiz superstar!</p>
      {:else if maxAttempts}
        <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-4">
          Quiz Results
        </h2>
        <p class="text-xl text-gray-800 mb-6">
          Score: {$quiz2Store.score}/{maxScore} | Points: {$quiz2Store.totalPoints}/{maxPoints}. You've used all {$quiz2Store.maxAttempts} attempts. Check your answers below and continue!
        </p>
      {:else}
        <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-4">
          Quiz Results
        </h2>
        <p class="text-xl text-gray-800 mb-6">
          Score: {$quiz2Store.score}/{maxScore} | Points: {$quiz2Store.totalPoints}/{maxPoints} (Attempt {$quiz2Store.attempts}/{$quiz2Store.maxAttempts}). Check your answers below.
        </p>
      {/if}
  
      <!-- Answer Sheet -->
      <div class="mb-8 text-left">
        <h3 class="text-2xl font-bold text-purple-700 mb-4">Your Answers</h3>
        {#each results as result}
          <div class="mb-4 p-4 bg-gray-100 rounded-lg">
            <p class="text-lg font-semibold text-gray-800">{result.question}</p>
            <p class="text-md text-gray-600">
              Your Answer: <span class="font-medium">{result.answer}</span>
              {#if result.isCorrect}
                <span class="text-green-600 font-bold"> ✓ Correct (+{result.points} points)</span>
              {:else}
                <span class="text-red-600 font-bold"> ✗ Incorrect (0 points)</span>
              {/if}
            </p>
          </div>
        {/each}
      </div>
  
      <!-- Buttons -->
      <div class="flex justify-center gap-4">
        {#if !isPerfect && !maxAttempts}
          <button
            on:click={handleRetake}
            class="bg-gradient-to-r from-yellow-400 to-orange-400 text-purple-900 font-bold px-6 py-3 rounded-full hover:scale-105 transition-transform"
          >
            Retake Quiz
          </button>
        {/if}
        <button
          on:click={handleContinue}
          class="bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold px-6 py-3 rounded-full hover:scale-105 transition-transform"
        >
          Continue
        </button>
      </div>
    </div>
  </div>
  
  <style>
 
  </style>