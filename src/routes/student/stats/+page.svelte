<!-- src/routes/student/stats/+page.svelte -->
<script lang="ts">
  import { slide, scale } from 'svelte/transition'; // Svelte transitions
  import { studentData } from '$lib/store/student_data'; // Import studentData store

  // Placeholder quiz scores (replace with actual data if available)
  const quizScores = {
    1: $studentData?.studentLevel >= 1 ? '8/10' : 'Not Taken',
    2: $studentData?.studentLevel >= 2 ? 'Not Taken' : 'Not Taken',
    3: $studentData?.studentLevel >= 3 ? 'Not Taken' : 'Not Taken',
  };

  // Placeholder items (based on studentRibbon, adjust as needed)
  const items = $studentData?.studentRibbon
    ? ['Map', 'Compass', 'Key'].slice(0, $studentData.studentRibbon)
    : [];

  // Get level display text
  function getLevelText(): string {
    if (!$studentData || $studentData.studentLevel === 0) return 'Level 0 - Not Started';
    return `Level ${$studentData.studentLevel} - ${$studentData.studentProgress}% Done!`;
  }
</script>

<div
  in:slide={{ duration: 400, y: 20 }}
  class="w-full max-w-4xl mx-auto p-6 text-center bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50 rounded-3xl shadow-lg flex flex-col items-center"
>
  <!-- Header -->
  <h1
    class="text-4xl md:text-5xl font-extrabold text-purple-600 mb-8 animate-bounce-slow"
    in:scale={{ duration: 600, start: 0.9 }}
    style="font-family: 'Comic Sans MS', 'Chalkboard', cursive;"
  >
    Your Super Stats! 🌟
  </h1>

  <!-- Stats Grid -->
  <div class="grid grid-cols-2 md:grid-cols-3 gap-6 w-full">
    <!-- Level Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-pink-500 active:bg-pink-50"
      in:scale={{ duration: 400, delay: 100 }}
    >
      <h2 class="text-xl font-bold text-pink-600 mb-2 flex items-center justify-center">
        <span class="mr-2">🏆</span> Level
      </h2>
      {#if $studentData && $studentData.studentLevel > 0}
        <div class="w-full bg-gray-100 rounded-full h-3 mb-2 overflow-hidden">
          <div
            class="bg-pink-400 h-full rounded-full transition-all duration-500"
            style="width: {$studentData.studentProgress}%;"
          ></div>
        </div>
      {/if}
      <p class="text-sm font-semibold text-gray-700">{getLevelText()}</p>
    </div>

    <!-- Quiz 1 Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-blue-500 active:bg-blue-50"
      in:scale={{ duration: 400, delay: 200 }}
    >
      <h2 class="text-xl font-bold text-blue-600 mb-2 flex items-center justify-center">
        <span class="mr-2">🧠</span> Quiz 1
      </h2>
      <p class="text-sm font-semibold text-gray-700">Score: {quizScores[1]}</p>
    </div>

    <!-- Quiz 2 Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-yellow-500 active:bg-yellow-50"
      in:scale={{ duration: 400, delay: 300 }}
    >
      <h2 class="text-xl font-bold text-yellow-600 mb-2 flex items-center justify-center">
        <span class="mr-2">📝</span> Quiz 2
      </h2>
      <p class="text-sm font-semibold text-gray-700">Score: {quizScores[2]}</p>
    </div>

    <!-- Quiz 3 Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-purple-500 active:bg-purple-50"
      in:scale={{ duration: 400, delay: 400 }}
    >
      <h2 class="text-xl font-bold text-purple-600 mb-2 flex items-center justify-center">
        <span class="mr-2">🔍</span> Quiz 3
      </h2>
      <p class="text-sm font-semibold text-gray-700">Score: {quizScores[3]}</p>
    </div>

    <!-- Trash Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-teal-500 active:bg-teal-50"
      in:scale={{ duration: 400, delay: 500 }}
    >
      <h2 class="text-xl font-bold text-teal-600 mb-2 flex items-center justify-center">
        <span class="mr-2">🗑️</span> Trash
      </h2>
      <p class="text-sm font-semibold text-gray-700">
        {$studentData?.studentColtrash || 0} Pieces
      </p>
    </div>

    <!-- Items Card -->
    <div
      class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer active:border-2 active:border-orange-500 active:bg-orange-50"
      in:scale={{ duration: 400, delay: 600 }}
    >
      <h2 class="text-xl font-bold text-orange-600 mb-2 flex items-center justify-center">
        <span class="mr-2">🎁</span> Items
      </h2>
      <p class="text-sm font-semibold text-gray-700">
        {$studentData?.studentRibbon || 0} Found
      </p>
      {#if items.length > 0}
        <p class="text-xs text-gray-600 mt-1">{items.join(', ')}</p>
      {/if}
    </div>
  </div>
</div>

<style>
  /* Smooth hover scale */
  .hover\:scale-105:hover {
    transform: scale(1.05);
  }

  /* Smooth transitions */
  .transition-all {
    transition: all 0.3s ease;
  }

  /* Active state for click */
  .active\:border-2:active {
    border-width: 2px;
  }

  /* Custom bounce animation for header */
  @keyframes bounce-slow {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-10px);
    }
  }
  .animate-bounce-slow {
    animation: bounce-slow 2s infinite;
  }
</style>