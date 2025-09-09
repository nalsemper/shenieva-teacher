<script>
    const slide = {
        text: "Maria's Promise - Slide 5 🌟",
        image: "/src/assets/LEVEL_1/STORY_1/PIC5.jpg"
    };
</script>

<div class="flex flex-col justify-center items-center text-center slide">
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
        {slide.text}
    </p>
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
