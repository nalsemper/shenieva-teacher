<script>
    const slide = {
        text: "Shenievia Reads stands at the edge of Readville Village. 'Time to head home!' she says with a grin. 🌟",
        image: "/src/assets/readville.gif" // Using your updated GIF
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
        will-change: opacity; /* Hint to browser for smoother animation */
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
        will-change: opacity; /* Optimize text animation */
        -webkit-font-smoothing: antialiased; /* Improve text clarity */
        -moz-osx-font-smoothing: grayscale; /* Improve text clarity */
        transform: translateZ(0); /* Force hardware acceleration without blur */
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes textFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    :global(.slide) {
        animation: fadeOut 1000ms ease-out forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>