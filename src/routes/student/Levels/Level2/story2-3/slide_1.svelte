<script>
    import { fade } from 'svelte/transition';
    import { onMount, onDestroy } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';
    import { language, narratorSpeed } from '$lib/store/story_lang_audio';

    const story = {
        title: {
            english: "Royce Choice",
            cebuano: "Ang Gipili ni Royce"
        },
        image: '/converted/assets/LEVEL_2/STORY_3/Pic8.webp'
    };

    // Audio state
    let audioEl;
    let containerEl;
    let isPlaying = false;
    let observer;
    let storyModeActive = false;
    let playToken = 0;

    // Title audio (no speed variants)
    const audioSrc = '/assets/audio/story-telling/Level_2/story_3/title/ROYCE CHOICE TITLE.mp3';

    function enterStoryMode() {
        if (!storyModeActive) {
            storyModeActive = true;
            audioStore.lockVolume(0.09);
        }
    }

    function exitStoryMode() {
        if (storyModeActive) {
            storyModeActive = false;
            audioStore.unlockVolume();
        }
    }

    function startNarration() {
        if (!audioEl) return;
        enterStoryMode();
        const token = ++playToken;
        audioEl.pause();
        audioEl.src = audioSrc;
        audioEl.load();
        setTimeout(() => {
            if (token !== playToken) return;
            audioEl.play().catch(e => console.warn('[story] Play blocked:', e));
        }, 150);
    }

    onMount(() => {
        if (typeof IntersectionObserver !== 'undefined' && containerEl) {
            observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                            startNarration();
                        } else {
                            if (audioEl && !audioEl.paused) {
                                audioEl.pause();
                            }
                        }
                    });
                },
                { threshold: 0.5 }
            );
            observer.observe(containerEl);
        }

        const handleGesture = () => {
            if (audioEl && audioEl.paused && containerEl) {
                const rect = containerEl.getBoundingClientRect();
                const vh = window.innerHeight;
                if (rect.top < vh && rect.bottom > 0) {
                    startNarration();
                }
            }
        };
        window.addEventListener('pointerdown', handleGesture, { once: true });
        window.addEventListener('keydown', handleGesture, { once: true });

        return () => {
            window.removeEventListener('pointerdown', handleGesture);
            window.removeEventListener('keydown', handleGesture);
        };
    });

    onDestroy(() => {
        if (observer && containerEl) {
            observer.unobserve(containerEl);
            observer.disconnect();
        }
        if (audioEl && !audioEl.paused) {
            audioEl.pause();
        }
        exitStoryMode();
    });
</script>

<div class="slide-container" bind:this={containerEl}>
    <h1 class="title">
        {$language === 'english' ? story.title.english : story.title.cebuano}
    </h1>

    <div class="image-wrapper">
        <img src={story.image} alt="Story scene" class="story-image" />
    </div>

    <div class="story-text" aria-hidden="true"></div>

    <audio 
        bind:this={audioEl} 
        on:play={() => isPlaying = true}
        on:pause={() => isPlaying = false}
        on:ended={exitStoryMode}
    ></audio>
</div>

<style>
    .slide-container {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        box-sizing: border-box;
    }

    .title {
        font-size: clamp(1.5rem, 3vw, 2.5rem);
        font-weight: bold;
        color: #1e40af;
        text-align: center;
        margin: 0;
    }

    .image-wrapper {
        flex: 1;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 0;
    }

    .story-image {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 0.5rem;
    }

    .story-text {
        font-size: clamp(1rem, 2vw, 1.25rem);
        line-height: 1.6;
        color: #1f2937;
        text-align: center;
        padding: 0 1rem;
        margin: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .slide-container {
        animation: fadeIn 0.5s ease-in-out;
    }
</style>