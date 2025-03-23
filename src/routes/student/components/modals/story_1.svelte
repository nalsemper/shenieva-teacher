<script>
    import { slide, fade } from "svelte/transition"; // Svelte transitions
    import Slide1 from "../../Stories/Story1/slide_1.svelte"; // Import Slide 1
    import Slide2 from "../../Stories/Story1/slide_2.svelte"; // Import Slide 2
    import Slide3 from "../../Stories/Story1/slide_3.svelte"; // Import Slide 3
  
    export let showModal = false; // Prop to control visibility
    export let onClose = () => {}; // Prop for closing the modal
  
    let currentSlide = 0; // Track the current slide index (0, 1, 2)
    let isLoading = true; // Track loading state
  
    const totalSlides = 3; // Total number of slides
  
    // Simulate loading delay when modal opens
    $: if (showModal && isLoading) {
        setTimeout(() => {
            isLoading = false; // Hide loader after 1 second
        }, 1000);
    }
  
    function nextSlide() {
        if (currentSlide < totalSlides - 1) {
            currentSlide += 1; // Move to the next slide
        }
    }
  
    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide -= 1; // Move to the previous slide
        }
    }
  
    function closeModal() {
        onClose(); // Call the parent’s close callback
        showModal = false; // Close the modal
        currentSlide = 0; // Reset to first slide
        isLoading = true; // Reset loading state for next open
    }
  
    function startQuiz() {
        alert("Quiz time! (Add your quiz logic here)"); // Replace with actual quiz logic
    }
</script>

{#if showModal}
  <!-- Loading Overlay -->
  {#if isLoading}
    <div
      class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50"
      transition:fade={{ duration: 300 }}
    >
      <div
        class="w-[10vw] h-[10vw] max-w-[60px] max-h-[60px] border-[1vw] border-t-lime-500 border-gray-300 rounded-full animate-spin"
      ></div>
      <p class="absolute text-[4vw] md:text-xl text-white mt-[15vh] font-bold">
        Loading Adventure... ✨
      </p>
    </div>
  {/if}

  <!-- Visual Novel Modal -->
  {#if !isLoading}
    <div
      class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
      transition:slide={{ duration: 300 }}
    >
      <div
        class="bg-white p-0 rounded-[3vw] shadow-2xl w-[250vw] max-w-[1100px] h-[85vh] flex flex-col items-center justify-between relative"
        transition:fade={{ duration: 500 }}
      >
        <!-- Center Div for Visual Novel Content -->
        <div class="w-[100%] h-[100%] flex flex-col items-center justify-center">
          {#key currentSlide}
            {#if currentSlide === 0}
              <Slide1 />
            {:else if currentSlide === 1}
              <Slide2 />
            {:else if currentSlide === 2}
              <Slide3 />
            {/if}
          {/key}
        </div>

        <!-- Navigation Arrows -->
        <button
          class="absolute right-[1vw] top-1/2 transform -translate-y-1/2 text-[8vw] md:text-6xl text-lime-500 hover:text-lime-600 transition-transform duration-200"
          on:click={nextSlide}
          disabled={currentSlide === totalSlides - 1}
        >
          ➡️
        </button>
        {#if currentSlide > 0}
          <button
            class="absolute left-[1vw] top-1/2 transform -translate-y-1/2 text-[8vw] md:text-6xl text-lime-500 hover:text-lime-600 transition-transform duration-200"
            on:click={prevSlide}
            in:fade={{ duration: 300 }}
          >
            ⬅️
          </button>
        {/if}

      </div>
    </div>

    <!-- Floating Exit Button (outside modal) -->
    <button
      class="fixed top-[2vh] right-[2vw] bg-red-500 text-white rounded-full w-[10vw] h-[10vw] max-w-[60px] max-h-[60px] flex items-center justify-center text-[5vw] md:text-2xl shadow-lg hover:bg-red-600 transition-all duration-200 z-50 exit-button"
      on:click={closeModal}
      in:fade={{ duration: 300 }}
    >
      ✕
    </button>
  {/if}
{/if}

<style>
  /* Disabled Button Styling */
  button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Spinner Animation */
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }

  /* Button Click Animation */
  button:active:not(:disabled) {
    transform: scale(1.1);
  }

  /* Exit Button Hover Effect (specific to exit button) */
  .exit-button:hover {
    transform: scale(1.1);
  }
</style>