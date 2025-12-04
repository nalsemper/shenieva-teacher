<script>
    import { fade } from "svelte/transition";
    import { language } from "$lib/store/story_lang_audio";
    import { onDestroy, onMount } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';

    const story = {
        title: {
            english: "Hector's Health",
            cebuano: "Ang Panlawas ni Hector"
        },
        image: '/converted/assets/LEVEL_2/STORY_1/PIC6.webp'
    };

    // Audio state
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;

    // Audio source for title narration
    const audioSrc = '/assets/audio/story-telling/Level_2/story_1/title/HECTOR_S HEALTH TITLE.mp3';

    // play/pause toggle
    function togglePlay() {
        if (!audioEl) return;
        if (isPlaying) {
            audioEl.pause();
            isPlaying = false;
        } else {
            safeStart(audioSrc);
        }
    }

    onDestroy(() => {
        if (audioEl) {
            try { audioEl.pause(); } catch (e) {}
            audioEl = null;
        }
    });

    // Story mode state and BGM ducking (duck to 9%)
    /** @type {number | null} */
    let _savedBgmVolume = null;
    /** @type {boolean} */
    let storyModeActive = false;
    /** @type {number | null} */
    let _startTimer = null;
    /** @type {number} */
    let playToken = 0;
    
    /** @param {string} src */
    function safeStart(src) {
        if (!audioEl) return;
        if (_startTimer) { clearTimeout(_startTimer); _startTimer = null; }
        playToken++;
        const localToken = playToken;
        try { audioEl.pause(); } catch (e) {}
        audioEl.src = src;
        _startTimer = setTimeout(() => {
            if (!audioEl) { _startTimer = null; return; }
            if (localToken !== playToken) { _startTimer = null; return; }
            audioEl.play().then(() => { isPlaying = true; }).catch((/** @type {any} */ err) => { console.warn('safeStart play failed', err); isPlaying = false; });
            _startTimer = null;
        }, 150);
    }

    function enterStoryMode() {
        if (!storyModeActive) {
            try { audioStore.init(); } catch (e) {}
            try { _savedBgmVolume = audioStore.getVolume(); } catch (e) { _savedBgmVolume = 0.7; }
            try { audioStore.lockVolume(0.09); } catch (e) { audioStore.setVolume(0.09, true); }
            storyModeActive = true;
        }
    }

    function exitStoryMode() {
        if (storyModeActive) {
            const v = typeof _savedBgmVolume === 'number' ? _savedBgmVolume : 0.7;
            try { audioStore.unlockVolume(); } catch (e) { audioStore.setVolume(v, true); }
            _savedBgmVolume = null;
            storyModeActive = false;
        }
    }

    // container ref to detect visibility
    let containerEl = null;
    /** @type {IntersectionObserver | null} */
    let io = null;

    function startNarration() {
        if (!audioEl) return;
        console.log('[story] startNarration called, audioEl:', audioEl, 'audioSrc:', audioSrc);
        enterStoryMode();
        safeStart(audioSrc);
    }

    // autoplay narration when component mounts and when slide becomes visible
    onMount(() => {
        setTimeout(() => { console.log('[story] onMount - attempting startNarration'); startNarration(); }, 80);

        if (typeof IntersectionObserver !== 'undefined') {
            io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    console.log('[story] IntersectionObserver entry', entry.intersectionRatio, entry.isIntersecting);
                    if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                        startNarration();
                    }
                });
            }, { threshold: [0, 0.5, 1] });

            if (containerEl) {
                io.observe(containerEl);
            }
        }

        return () => {
            exitStoryMode();
            if (io) {
                io.disconnect();
                io = null;
            }
        };
    });
</script>

<audio bind:this={audioEl} on:ended={() => { isPlaying = false; exitStoryMode(); }} style="display:none;"></audio>

<div class="slide-container" bind:this={containerEl}>
    <h1 class="title">
        {$language === 'english' ? story.title.english : story.title.cebuano}
    </h1>

    <div class="image-wrapper">
        <img src={story.image} alt="Story scene" class="story-image" />
    </div>

    <div class="story-text" aria-hidden="true"></div>
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
