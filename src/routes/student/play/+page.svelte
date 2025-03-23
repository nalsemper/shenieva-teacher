<script>
  import { name } from "$lib/store/nameStore.js"; // Import the global name store
  import { slide } from "svelte/transition"; // Svelte transition
  import Story1 from "../components/modals/story_1.svelte"; // Import the Story 1 modal component

  let isClicked = false; // Track if the Start button is clicked
  let isWiggling = true; // Track if the initial wiggle is active
  let showModal = false; // Control the visibility of the modal

  // Switch from wiggle to steady after the initial wiggle completes
  setTimeout(() => {
    isWiggling = false;
  }, 500); // Matches the 0.5s duration of the wiggle animation

  function handleStartClick() {
    isClicked = true;
    showModal = true; // Show the Story 1 modal when Start is clicked
  }

  function handleCloseModal() {
    showModal = false;
    isClicked = false; // Reset the button state when closing the modal
  }
</script>

<div in:slide={{ duration: 400 }} class="text-center">
  <h1 class="text-[6vw] md:text-4xl font-bold text-lime-500 mb-[2vh] animate-bounce">
    Hi, {$name}! 🌟
  </h1>
  <h2 class="text-[5vw] md:text-3xl font-bold text-lime-500 mb-[2vh]">
    Welcome to Readville Village! 📖✨
  </h2>
  <p class="text-[3vw] md:text-xl text-gray-700 mb-[1vh]">
    Join Shenievia Reads on an exciting journey home!<br> Explore fun stories, answer questions, and earn rewards along the way.
  </p>
  <p class="text-[3vw] md:text-xl text-gray-700 mb-[3vh]">
    Think, learn, and discover the hidden lessons in every tale.
  </p>
  <p class="text-[4vw] md:text-xl text-orange-500 mb-[5vh] font-bold">
    Your adventure starts now! 🚀📚
  </p>
  <button
    class="px-[3vw] py-[1.5vh] rounded-full text-[3vw] md:text-xl font-bold text-white bg-lime-400 border-[0.5vw] border-white shadow-[0_0.75vw_1.5vw_rgba(0,0,0,0.4),inset_0_0.5vw_0.75vw_rgba(255,255,255,0.7),inset_0_-0.5vw_0.75vw_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-lime-500"
    class:animate-wiggle={isWiggling && !isClicked}
    class:animate-steady={!isWiggling && !isClicked}
    class:scale-150={isClicked}
    class:shadow-[0_1vw_2vw_rgba(0,0,0,0.5),inset_0_0.5vw_0.75vw_rgba(255,255,255,0.7),inset_0_-0.5vw_0.75vw_rgba(0,0,0,0.3)]={isClicked}
    on:click={handleStartClick}
  >
    Start
  </button>
</div>

<!-- Use the Story1 Modal -->
<Story1 {showModal} onClose={handleCloseModal} />

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