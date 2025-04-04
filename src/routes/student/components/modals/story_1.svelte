<script lang="ts">
  import { slide, fade } from "svelte/transition";
  import Slide1 from "../../Stories/Story1/slide_1.svelte";
  import Slide2 from "../../Stories/Story1/slide_2.svelte";
  import Slide3 from "../../Stories/Story1/slide_3.svelte";

  type SvelteComponent = any;

  let VendorSlide: SvelteComponent | null = null;
  async function loadVendorSlide(path: string): Promise<void> {
      try {
          const module = await import(/* @vite-ignore */ path);
          VendorSlide = module.default;
      } catch (error) {
          console.error("Failed to load vendor slide:", error);
          VendorSlide = null;
      }
  }

  export let showModal: boolean = false;
  export let onClose: () => void = () => {};

  let currentSlide: number = 0;
  let isLoading: boolean = true;
  let selectedVendorPath: string | null = null;
  let isVendorSelected: boolean = false;
  let vendorSlideIndex: number = 0;
  let totalVendorSlides: number = 2; // 3 slides per vendor (0-2)

  const baseSlides: SvelteComponent[] = [Slide1, Slide2, Slide3];
  let totalSlides: number = baseSlides.length - 1; // Now 2 (0-2)

  $: if (showModal && isLoading) {
      setTimeout(() => {
          isLoading = false;
      }, 1000);
  }

  function nextSlide(): void {
      if (isVendorSelected) {
          if (vendorSlideIndex < totalVendorSlides) {
              vendorSlideIndex += 1;
          }
      } else if (currentSlide < totalSlides) {
          if (currentSlide !== 2) { // Not on vendor selection slide
              currentSlide += 1;
          }
      }
  }

  function prevSlide(): void {
      if (isVendorSelected) {
          if (vendorSlideIndex > 0) {
              vendorSlideIndex -= 1;
          } else {
              // Go back to Slide3
              isVendorSelected = false;
              VendorSlide = null;
              currentSlide = 2;
          }
      } else if (currentSlide > 0) {
          currentSlide -= 1;
      }
  }

  function closeModal(): void {
      onClose();
      showModal = false;
      currentSlide = 0;
      isLoading = true;
      VendorSlide = null;
      selectedVendorPath = null;
      isVendorSelected = false;
      vendorSlideIndex = 0;
      totalSlides = baseSlides.length - 1;
  }

  interface VendorSelectEvent extends CustomEvent {
      detail: { vendorId: number; nextPath: string };
  }

  function handleVendorSelection(event: VendorSelectEvent): void {
      const { nextPath } = event.detail;
      selectedVendorPath = nextPath;
      loadVendorSlide(nextPath).then(() => {
          if (VendorSlide) {
              isVendorSelected = true;
              vendorSlideIndex = 0;
              // Update the path for subsequent vendor slides
              const basePath = nextPath.substring(0, nextPath.lastIndexOf('/'));
              loadVendorSlide(`${basePath}/slide_${vendorSlideIndex + 1}.svelte`);
          }
      });
  }

  $: currentVendorSlidePath = isVendorSelected && selectedVendorPath
      ? `${selectedVendorPath.substring(0, selectedVendorPath.lastIndexOf('/'))}/slide_${vendorSlideIndex + 1}.svelte`
      : null;

  $: if (currentVendorSlidePath && isVendorSelected) {
      loadVendorSlide(currentVendorSlidePath);
  }
</script>

{#if showModal}
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

  {#if !isLoading}
      <div
          class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
          transition:slide={{ duration: 300 }}
      >
          <div
              class="bg-white p-0 rounded-[3vw] shadow-2xl w-[250vw] max-w-[1100px] h-[85vh] flex flex-col items-center justify-between relative"
              transition:fade={{ duration: 500 }}
          >
              <div class="w-[100%] h-[100%] flex flex-col items-center justify-center">
                  {#key isVendorSelected ? `${currentSlide}-${vendorSlideIndex}` : currentSlide}
                      {#if !isVendorSelected}
                          {#if currentSlide === 0}
                              <Slide1 />
                          {:else if currentSlide === 1}
                              <Slide2 />
                          {:else if currentSlide === 2}
                              <Slide3 on:vendorSelected={handleVendorSelection} />
                          {/if}
                      {:else if VendorSlide}
                          <svelte:component this={VendorSlide} />
                      {/if}
                  {/key}
              </div>

              <button
                  class="absolute right-[1vw] top-1/2 transform -translate-y-1/2 text-[8vw] md:text-6xl text-lime-500 hover:text-lime-600 transition-transform duration-200"
                  on:click={nextSlide}
                  disabled={(!isVendorSelected && currentSlide === 2) || 
                           (isVendorSelected && vendorSlideIndex >= totalVendorSlides)}
              >
                  ➡️
              </button>
              {#if currentSlide > 0 || (isVendorSelected && vendorSlideIndex > 0)}
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
  button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
  }

  @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
  }
  .animate-spin {
      animation: spin 1s linear infinite;
  }

  button:active:not(:disabled) {
      transform: scale(1.1);
  }

  .exit-button:hover {
      transform: scale(1.1);
  }
</style>