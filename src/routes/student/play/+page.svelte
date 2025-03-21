<script>
    import { name } from "$lib/store/nameStore.js"; // Import the global name store
    import { slide } from "svelte/transition"; // Svelte transition
  
    let isClicked = false; // Track if the Start button is clicked
    let isWiggling = true; // Track if the initial wiggle is active
  
    // Switch from wiggle to steady after the initial wiggle completes
    setTimeout(() => {
      isWiggling = false;
    }, 500); // Matches the 0.5s duration of the wiggle animation
  
    function handleStartClick() {
      isClicked = true;
      // Optionally, add logic here to start the adventure (e.g., navigation)
    }
  </script>
  
  <div in:slide={{ duration: 400 }} class="text-center">
    <h1 class="text-4xl font-bold text-lime-500 mb-4 animate-bounce">
      Hi, {$name}! 🌟
    </h1>
    <h2 class="text-3xl font-bold text-lime-500 mb-4">
      Welcome to Readville Village! 📖✨
    </h2>
    <p class="text-xl text-gray-700 mb-2">
      Join Shenievia on an exciting journey home! Explore fun stories, answer questions, and earn rewards along the way.
    </p>
    <p class="text-xl text-gray-700 mb-10">
      Think, learn, and discover the hidden lessons in every tale.
    </p>
    <p class="text-xl text-orange-500 mb-12 font-bold">
      Your adventure starts now! 🚀📚
    </p>
    <button
      class="px-6 py-3 rounded-full text-xl font-bold text-white bg-lime-400 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-lime-500"
      class:animate-wiggle={isWiggling && !isClicked}
      class:animate-steady={!isWiggling && !isClicked}
      class:scale-150={isClicked}
      class:shadow-[0_8px_16px_rgba(0,0,0,0.5),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]={isClicked}
      on:click={handleStartClick}
    >
      Start
    </button>
  </div>
  
  <style>
    /* Initial Wiggle Animation */
    @keyframes wiggle {
      0% { transform: rotate(0deg); }
      25% { transform: rotate(5deg); }
      50% { transform: rotate(-5deg); }
      75% { transform: rotate(5deg); }
      100% { transform: rotate(0deg); }
    }
    .animate-wiggle {
      animation: wiggle 0.5s ease-in-out 1;
    }
  
    /* Steady Loop Animation */
    @keyframes steady {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
    .animate-steady {
      animation: steady 1.5s ease-in-out infinite;
    }
  </style>