<script>
    import { onMount, onDestroy } from 'svelte';
    import { isFast } from '$lib/store/story_lang_audio';

    const slide = {
        text: "Slide 3 ni - Shenievia Reads stands at the edge of Readville Village. 'Time to head home!' she says with a grin. 🌟",
        audioFast: '/src/assets/audio/fast.mp3',
        audioSlow: '/src/assets/audio/slow.mp3',
        image: "/src/assets/readville.gif"
    };

    let currentIsFast;
    let audio;
    let isPlaying = false;

    isFast.subscribe(value => {
        currentIsFast = value;
        updateAudio();
    });

    $: currentText = slide.text;

    function updateAudio() {
        if (audio) audio.pause();
        audio = new Audio(
            currentIsFast 
                ? slide.audioFast 
                : slide.audioSlow
        );
        if (isPlaying) playAudio();
    }

    onMount(() => {
        updateAudio();
        playAudio();
    });

    onDestroy(() => {
        stopAudio();
    });

    function playAudio() {
        stopAudio();
        audio.currentTime = 0;
        audio.play();
        isPlaying = true;
    }

    function stopAudio() {
        if (audio) audio.pause();
        isPlaying = false;
    }

    function repeatSlide() {
        playAudio();
    }
</script>

<div class="flex flex-col justify-center items-center text-center slide relative">
    {#if slide.image}
        <div class="image-container">
            <img
                src={slide.image}
                alt="Story Scene"
                class="block mx-auto rounded-[2vw] shadow-lg"
            />
        </div>
    {/if}
    <p class="text-[4vw] md:text-2xl text-gray-800 font-semibold text-fade">
        {currentText}
    </p>

    <!-- Kid-Friendly Controls -->
    <div class="controls absolute top-0 right-4 flex flex-col gap-2">
        <button 
            on:click={repeatSlide}
            class="kid-button bg-yellow-400 hover:bg-yellow-500 repeat-button"
        >
            <span class="icon">🔄</span>
        </button>
    </div>
</div>

<style>
    .slide {
        animation: fadeIn 1000ms ease-in forwards;
        will-change: opacity;
    }

    .image-container {
        width: 80vw;
        height: 80vh;
        max-width: 800px;
        max-height: 400px;
        margin-bottom: 2vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .text-fade {
        white-space: pre-wrap;
        overflow-wrap: break-word;
        max-width: 80%;
        animation: textFadeIn 1000ms ease-in forwards;
        will-change: opacity;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transform: translateZ(0);
    }

    .kid-button {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kid-button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .repeat-button:active {
        animation: rotateRightToLeft 0.5s ease;
    }

    .speed-button.fast:active {
        animation: rotateLeftToRight 0.5s ease;
    }

    .speed-button.slow:active {
        animation: rotateRightToLeft 0.5s ease;
    }

    .icon {
        font-size: 1.5rem;
        line-height: 1;
    }

    .repeat-button {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }

    .speed-button {
        background: linear-gradient(135deg, #c084fc, #9333ea);
    }

    @keyframes rotateRightToLeft {
        0% { transform: rotate(0deg); }
        50% { transform: rotate(-180deg); }
        100% { transform: rotate(-360deg); }
    }

    @keyframes rotateLeftToRight {
        0% { transform: rotate(0deg); }
        50% { transform: rotate(180deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes textFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    :global(.slide) {
        animation: fadeOut 1000ms ease-out forwards;
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>