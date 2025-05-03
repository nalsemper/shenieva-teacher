<script lang="ts">
    import { slide, fade } from "svelte/transition";
    import Slide1 from "../../Stories/Story2/slide_1.svelte";
    import Slide2 from "../../Stories/Story2/slide_2.svelte";
    import Slide3 from "../../Stories/Story2/slide_3.svelte";
    import Slide4 from "../../Stories/Story2/slide_4.svelte"; // Add more slides as needed
    import { language } from "$lib/store/story_lang_audio";

    export let showModal: boolean = false;
    export let onClose: () => void = () => {};

    let currentSlide: number = 0;
    let isLoading: boolean = true;
    let showLanguageModal: boolean = false;

    // Define all slides in an array
    const slides = [Slide1, Slide2, Slide3, Slide4]; // Add more slides here (e.g., Slide5, Slide6, etc.)
    const totalSlides: number = slides.length - 1;

    // Handle loading state when modal opens
    $: if (showModal && isLoading) {
        setTimeout(() => {
            isLoading = false;
        }, 1000);
    }

    function nextSlide(): void {
        if (currentSlide < totalSlides) {
            currentSlide += 1;
        }
    }

    function prevSlide(): void {
        if (currentSlide > 0) {
            currentSlide -= 1;
        }
    }

    function closeModal(): void {
        onClose();
        showModal = false;
        currentSlide = 0;
        isLoading = true;
        showLanguageModal = false;
    }

    function toggleLanguageModal(): void {
        showLanguageModal = !showLanguageModal;
    }

    function setLanguage(lang: string): void {
        language.set(lang);
        showLanguageModal = false;
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
                    {#key currentSlide}
                        <svelte:component this={slides[currentSlide]} />
                    {/key}
                </div>

                <!-- Language Button with Active Language Indicator -->
                <div class="language-button-container">
                    <button 
                        on:click={toggleLanguageModal}
                        class="language-button bg-blue-500 hover:bg-blue-600"
                    >
                        <span class="icon">{$language === 'english' ? '🔠' : '🔠'}</span>
                    </button>
                </div>

                <!-- Language Modal with Active Highlight -->
                {#if showLanguageModal}
                    <div class="modal-overlay" on:click={toggleLanguageModal}>
                        <div class="modal-content" on:click|stopPropagation>
                            <h2 class="text-2xl font-bold mb-4">Choose Language</h2>
                            <button 
                                on:click={() => setLanguage('english')}
                                class="language-option"
                                class:active={$language === 'english'}
                            >
                                English
                            </button>
                            <button 
                                on:click={() => setLanguage('cebuano')}
                                class="language-option"
                                class:active={$language === 'cebuano'}
                            >
                                Cebuano
                            </button>
                        </div>
                    </div>
                {/if}

                <!-- Next Button -->
                <button
                    class="absolute right-[1vw] top-1/2 transform -translate-y-1/2 text-[8vw] md:text-6xl text-lime-500 hover:text-lime-600 transition-transform duration-200"
                    on:click={nextSlide}
                    disabled={currentSlide >= totalSlides}
                >
                    ➡️
                </button>

                <!-- Previous Button -->
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

        <!-- Exit Button -->
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

    /* Language Button Styles */
    .language-button-container {
        position: absolute;
        top: 1rem;
        right: 4rem;
        z-index: 1000;
    }

    .language-button {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease;
    }

    .language-button:hover {
        transform: scale(1.1);
    }

    .language-button:active {
        animation: bounce 0.3s ease;
    }

    .icon {
        font-size: 1.5rem;
        font-weight: bold;
    }

    /* Language Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .language-option {
        display: block;
        width: 100%;
        padding: 0.75rem;
        margin: 0.5rem 0;
        border-radius: 0.5rem;
        color: white;
        font-size: 1.25rem;
        font-weight: bold;
        transition: transform 0.2s ease;
        background-color: #10b98183; /* Default green */
    }

    .language-option:hover {
        transform: scale(1.05);
    }

    .language-option.active {
        background-color: #06d594; /* Darker green for active */
        border: 2px solid #047857; /* Border to emphasize active state */
    }

    @keyframes bounce {
        0% { transform: scale(1); }
        50% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
</style>