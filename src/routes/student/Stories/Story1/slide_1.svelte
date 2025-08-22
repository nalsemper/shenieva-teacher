<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    const dispatch = createEventDispatcher();

    const slide = {
        text: "Choose a story. 🌟",
    };

    const stories = [
        {
            id: 1,
            title: "Maria's Promise",
            image: "/src/assets/LEVEL_1/STORY_1/PIC1.jpg",
            nextPath: "/src/routes/student/Stories/Story1/slide_1.svelte"
        },
        {
            id: 2,
            title: "Candice and Candies",
            image: "/src/assets/LEVEL_1/STORY_2/PIC1.jpg",
            nextPath: "/src/routes/student/Stories/Story2/slide_1.svelte"
        },
        {
            id: 3,
            title: "Hannah, the Honest Vendor",
            image: "/src/assets/LEVEL_1/STORY_3/PIC1.jpg",
            nextPath: "/src/routes/student/Stories/Story3/slide_1.svelte"
        }
    ];

    function handleStorySelect(storyId: number): void {
        const selectedStory = stories.find(s => s.id === storyId);
        if (selectedStory) {
            dispatch('storySelected', { storyId, nextPath: selectedStory.nextPath });
        }
    }
</script>

<div class="flex flex-col justify-center items-center text-center slide">
    <p class="text-[6vw] md:text-3xl text-gray-800 font-semibold text-fade mb-8">
        {slide.text}
    </p>

    <div class="story-container">
        {#each stories as story}
            <button 
                class="story-button"
                on:click={() => handleStorySelect(story.id)}
            >
                <img
                    src={story.image}
                    alt={story.title}
                    class="story-image"
                />
                <span class="story-label">{story.title}</span>
            </button>
        {/each}
    </div>
</div>

<style>
    :global(body) {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        overflow-y: auto;
        background: none;
    }

    .slide {
        animation: fadeIn 1000ms ease-in forwards;
        will-change: opacity;
        padding: 2rem;
        box-sizing: border-box;
        max-width: 100vw;
        min-height: 100vh;
        position: relative;
        isolation: isolate;
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

    .story-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2.5rem;
        margin-top: 2rem;
        width: 85%;
        max-width: 900px;
        box-sizing: border-box;
    }

    .story-button {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 250px;
        max-width: 32%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }

    .story-button:hover, .story-button:focus {
        transform: scale(1.3);
        border-color: #4299e1;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .story-button:hover .story-image, .story-button:focus .story-image {
        max-height: 250px;
    }

    .story-image {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        margin-bottom: 0.5rem;
        transition: max-height 0.3s ease;
    }

    .story-label {
        color: #2d3748;
        font-size: 1.4rem;
        font-weight: 500;
        text-align: center;
        white-space: normal;
        line-height: 1.3;
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