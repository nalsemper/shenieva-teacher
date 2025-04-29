<script lang="ts">
    import { slide, fade } from "svelte/transition";
    import Slide1 from "../../Stories/Story3/slide_1.svelte";
    import Slide2 from "../../Stories/Story3/slide_2.svelte";
    import Slide3 from "../../Stories/Story3/slide_3.svelte";
    import { language } from "$lib/store/story_lang_audio";

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
    let totalVendorSlides: number = 2;
    let showLanguageModal: boolean = false;

    const baseSlides: SvelteComponent[] = [Slide1, Slide2, Slide3];
    let totalSlides: number = baseSlides.length - 1;

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
            if (currentSlide !== 2) {
                currentSlide += 1;
            }
        }
    }

    function prevSlide(): void {
        if (isVendorSelected) {
            if (vendorSlideIndex > 0) {
                vendorSlideIndex -= 1;
            } else {
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
        showLanguageModal = false;
    }

    function toggleLanguageModal(): void {
        showLanguageModal = !showLanguageModal;
    }

    function setLanguage(lang: string): void {
        language.set(lang);
        showLanguageModal = false;
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