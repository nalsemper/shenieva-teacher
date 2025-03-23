<script>
  import { slide, scale } from "svelte/transition"; // Svelte transitions

  let clickedCard = null; // Track which card is clicked

  function handleCardClick(card) {
    clickedCard = clickedCard === card ? null : card; // Toggle clicked state
  }
</script>

<div
  in:slide={{ duration: 400, y: 20 }}
  class="w-auto h-full text-center bg-gradient-to-br from-pink-200 via-yellow-200 to-blue-200 rounded-[2vw] shadow-2xl flex flex-col items-center overflow-hidden"
>
  <!-- Header -->
  <h1
    class="text-[4vw] md:text-[2rem] font-extrabold text-purple-600 mb-[2vh] animate-pulse drop-shadow-[0_0.25vw_0.5vw_rgba(0,0,0,0.3)]"
    in:scale={{ duration: 600, start: 0.8 }}
  >
    Super Stats Adventure! 🌟✨
  </h1>

  <!-- Stats Grid -->
  <div class="w-auto grid grid-cols-4 gap-[2vw] px-[2vw] pb-[2vw]">
    <!-- Level 1 with Progress Bar -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "level1"}
      in:scale={{ duration: 400, delay: 100 }}
      on:click={() => handleCardClick("level1")}
    >
      <div
        class="absolute inset-0 bg-pink-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "level1"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-pink-500 flex items-center justify-center">
          Level 1 <span class="ml-[0.5vw]">🎉</span>
        </h2>
        <div class="w-full bg-gray-200 rounded-full h-[1vh] mt-[1vh] overflow-hidden">
          <div
            class="bg-gradient-to-r from-pink-400 to-yellow-400 h-full rounded-full animate-pulse"
            style="width: 60%;"
          ></div>
        </div>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          60% Done!
        </p>
      </div>
    </div>

    <!-- Quiz 1 -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "quiz1"}
      in:scale={{ duration: 400, delay: 200 }}
      on:click={() => handleCardClick("quiz1")}
    >
      <div
        class="absolute inset-0 bg-blue-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "quiz1"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-blue-500 flex items-center justify-center">
          Quiz 1 <span class="ml-[0.5vw]">🧠</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          Score: 8/10
        </p>
      </div>
    </div>

    <!-- Level 2 -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "level2"}
      in:scale={{ duration: 400, delay: 300 }}
      on:click={() => handleCardClick("level2")}
    >
      <div
        class="absolute inset-0 bg-green-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "level2"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-green-500 flex items-center justify-center">
          Level 2 <span class="ml-[0.5vw]">🚀</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          Not Started
        </p>
      </div>
    </div>

    <!-- Quiz 2 -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "quiz2"}
      in:scale={{ duration: 400, delay: 400 }}
      on:click={() => handleCardClick("quiz2")}
    >
      <div
        class="absolute inset-0 bg-yellow-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "quiz2"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-yellow-500 flex items-center justify-center">
          Quiz 2 <span class="ml-[0.5vw]">📝</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          Not Taken
        </p>
      </div>
    </div>

    <!-- Level 3 -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "level3"}
      in:scale={{ duration: 400, delay: 500 }}
      on:click={() => handleCardClick("level3")}
    >
      <div
        class="absolute inset-0 bg-red-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "level3"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-red-500 flex items-center justify-center">
          Level 3 <span class="ml-[0.5vw]">🔒</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          Locked
        </p>
      </div>
    </div>

    <!-- Quiz 3 -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "quiz3"}
      in:scale={{ duration: 400, delay: 600 }}
      on:click={() => handleCardClick("quiz3")}
    >
      <div
        class="absolute inset-0 bg-purple-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "quiz3"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-purple-500 flex items-center justify-center">
          Quiz 3 <span class="ml-[0.5vw]">🔒</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          Locked
        </p>
      </div>
    </div>

    <!-- Collected Trash -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "trash"}
      in:scale={{ duration: 400, delay: 700 }}
      on:click={() => handleCardClick("trash")}
    >
      <div
        class="absolute inset-0 bg-teal-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "trash"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-teal-500 flex items-center justify-center">
          Trash <span class="ml-[0.5vw]">🗑️</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          12 Pieces
        </p>
      </div>
    </div>

    <!-- Items -->
    <div
      class="bg-white p-[2vw] rounded-[2vw] shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer relative overflow-hidden"
      class:scale-110={clickedCard === "items"}
      in:scale={{ duration: 400, delay: 800 }}
      on:click={() => handleCardClick("items")}
    >
      <div
        class="absolute inset-0 bg-orange-300 transform -translate-x-full transition-transform duration-500 ease-in-out"
        class:translate-x-0={clickedCard === "items"}
      ></div>
      <div class="relative z-10">
        <h2 class="text-[2vw] md:text-[1rem] font-bold text-orange-500 flex items-center justify-center">
          Items <span class="ml-[0.5vw]">🎁</span>
        </h2>
        <p class="text-[1.5vw] md:text-[0.75rem] font-semibold text-gray-600 mt-[0.5vh]">
          3 Found
        </p>
        <p class="text-[1.2vw] md:text-[0.65rem] text-gray-500">
          Map, Compass, Key
        </p>
      </div>
    </div>
  </div>
</div>

<style>
  /* Ensure hover scale works smoothly */
  .hover\:scale-105:hover {
    transform: scale(1.05);
  }
</style>