<!-- src/routes/student/components/modals/choose_story.svelte -->
<script>
    import { slide, scale } from "svelte/transition"; // Svelte transitions
  
    export let showModal = false; // Prop to control visibility
    export let onSelectStory = () => {}; // Prop for story selection callback
    export let onClose = () => {}; // Prop for closing the modal
  
    function handleStorySelect(story) {
      onSelectStory(story); // Call the parent’s callback with the selected story
      showModal = false; // Close the modal
    }
  
    function closeModal() {
      onClose(); // Call the parent’s close callback
      showModal = false; // Close the modal
    }
  </script>
  
  {#if showModal}
    <div
      class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
      transition:slide={{ duration: 300 }}
    >
      <div
        class="bg-white p-[3vw] rounded-[3vw] shadow-2xl w-[80vw] max-w-[400px] text-center"
        transition:scale={{ duration: 400, start: 0.9 }}
      >
        <h2 class="text-[5vw] md:text-3xl font-extrabold text-purple-600 mb-[2vh] animate-bounce">
          Pick a Story! 📚✨
        </h2>
        <p class="text-[3vw] md:text-lg text-gray-700 mb-[3vh]">
          Which adventure do you want to start?
        </p>
        <div class="flex flex-col gap-[2vh]">
          <!-- Story 1 Button -->
          <button
            class="px-[3vw] py-[1.5vh] rounded-full text-[3vw] md:text-xl font-bold text-white bg-pink-400 hover:bg-pink-500 transition-all duration-300 transform hover:scale-110 border-[0.5vw] border-white shadow-[0_0.5vw_1vw_rgba(0,0,0,0.3)]"
            on:click={() => handleStorySelect("Story 1")}
          >
            Story 1 🌈
          </button>
          <!-- Story 2 Button -->
          <button
            class="px-[3vw] py-[1.5vh] rounded-full text-[3vw] md:text-xl font-bold text-white bg-blue-400 hover:bg-blue-500 transition-all duration-300 transform hover:scale-110 border-[0.5vw] border-white shadow-[0_0.5vw_1vw_rgba(0,0,0,0.3)]"
            on:click={() => handleStorySelect("Story 2")}
          >
            Story 2 🚀
          </button>
          <!-- Story 3 Button -->
          <button
            class="px-[3vw] py-[1.5vh] rounded-full text-[3vw] md:text-xl font-bold text-white bg-yellow-400 hover:bg-yellow-500 transition-all duration-300 transform hover:scale-110 border-[0.5vw] border-white shadow-[0_0.5vw_1vw_rgba(0,0,0,0.3)]"
            on:click={() => handleStorySelect("Story 3")}
          >
            Story 3 🏰
          </button>
        </div>
        <button
          class="mt-[3vh] text-[2.5vw] md:text-lg text-gray-600 hover:text-gray-800 transition-colors duration-300"
          on:click={closeModal}
        >
          Go back! 🏠
        </button>
      </div>
    </div>
  {/if}
  
  <style>
    /* No additional styles needed here; all styling is included inline */
  </style>