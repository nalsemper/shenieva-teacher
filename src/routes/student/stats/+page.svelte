<script>
    import { slide, scale } from "svelte/transition"; // Svelte transitions
  </script>
  
  <div
    in:slide={{ duration: 400, y: 20 }}
    class="text-center p-4 sm:p-6 bg-gradient-to-br from-orange-200 via-yellow-100 to-lime-200 rounded-3xl shadow-2xl flex flex-col items-center w-full"
  >
    <h1
      class="text-3xl sm:text-4xl font-extrabold text-orange-500 mb-4 sm:mb-6 animate-bounce drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)]"
      in:scale={{ duration: 600, start: 0.8 }}
    >
      Your Super Stats! 🌟📊
    </h1>
  
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 w-full">
      <!-- Level 1 with Progress Bar -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 100 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Level 1 <span class="ml-1">🎉</span>
        </h2>
        <div class="w-full bg-gray-300 rounded-full h-3 sm:h-4 mt-2 overflow-hidden">
          <div
            class="bg-gradient-to-r from-orange-400 to-yellow-400 h-full rounded-full animate-pulse"
            style="width: 60%;"
          ></div>
        </div>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">60% Done!</p>
      </div>
  
      <!-- Quiz 1 -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 200 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Quiz 1 <span class="ml-1">🧠</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">Score: 8/10</p>
      </div>
  
      <!-- Level 2 -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 300 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Level 2 <span class="ml-1">🚀</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">Not Started</p>
      </div>
  
      <!-- Quiz 2 -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 400 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Quiz 2 <span class="ml-1">📝</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">Not Taken</p>
      </div>
  
      <!-- Level 3 -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 500 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Level 3 <span class="ml-1">🔒</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">Locked</p>
      </div>
  
      <!-- Quiz 3 -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 600 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Quiz 3 <span class="ml-1">🔒</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">Locked</p>
      </div>
  
      <!-- Collected Trash -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 700 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Trash <span class="ml-1">🗑️</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">12 Pieces</p>
      </div>
  
      <!-- Items -->
      <div
        class="bg-white p-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        in:scale={{ duration: 400, delay: 800 }}
      >
        <h2 class="text-lg sm:text-xl font-bold text-orange-600 flex items-center justify-center">
          Items <span class="ml-1">🎁</span>
        </h2>
        <p class="text-sm sm:text-base font-semibold text-gray-700 mt-1">3 Found</p>
        <p class="text-xs sm:text-sm text-gray-600">Map, Compass, Key</p>
      </div>
    </div>
  </div>
  
  <style>
    /* Ensure hover scale works */
    .hover\:scale-105:hover {
      transform: scale(1.05);
    }
  </style>