<script>
    import { fade } from "svelte/transition";
    import { onMount, onDestroy } from 'svelte';
    import { language, narratorSpeed } from "$lib/store/story_lang_audio";
    import { audioStore } from '$lib/store/audio_store';
    

    const story = {
        title: {
            english: "Candice and Candies",
            cebuano: "Si Candice ug ang mga Kendi"
        },
        text: {
            english: "There was once a child who loved candies, her name is Candice. She eats candies for breakfast, lunch and dinner.",
            cebuano: "Adunay tag-iya sa usa ka sari-sari nga tindahan nga ginganlan ug Lena. Nagaserbisyo siya sa mga kustomer matag adlaw uban ang usa ka pahiyom."
        },
    image: "/converted/assets/LEVEL_1/STORY_2/PIC1.webp"
    };

    // Audio state
    let speed = $narratorSpeed;
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;

    // Audio source with speed variants
    $: audioSrc = (() => {
        const base = '/assets/audio/story-telling/Level_1/story_2';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        return `${base}/${sp}/slide_1/C1.mp3`;
    })();

    let playToken = 0;
    let _startTimer = /** @type {ReturnType<typeof setTimeout> | null} */ (null);

    function safeStartAudio() {
        if (!audioEl) return;
        if (_startTimer) { clearTimeout(_startTimer); _startTimer = null; }
        const token = ++playToken;
        enterStoryMode();
        _startTimer = setTimeout(() => {
            if (!audioEl) { _startTimer = null; return; }
            if (token !== playToken) { _startTimer = null; return; }
            audioEl.play().catch(() => { isPlaying = false; });
            _startTimer = null;
        }, 150);
    }

    // Story mode state and BGM ducking (duck to 9%)
    /** @type {number | null} */
    let _savedBgmVolume = null;
    /** @type {boolean} */
    let storyModeActive = false;

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
    let containerEl = /** @type {HTMLDivElement | null} */ (null);
    /** @type {IntersectionObserver | null} */
    let io = null;

    // autoplay title when component mounts and when slide becomes visible
    onMount(() => {
        setTimeout(() => safeStartAudio(), 80);

        if (typeof IntersectionObserver !== 'undefined') {
            io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                        safeStartAudio();
                    }
                });
            }, { threshold: [0, 0.5, 1] });

            if (containerEl) {
                io.observe(containerEl);
            }
        }

        // If autoplay is blocked, listen for first user gesture
        const userGestureHandler = () => {
            safeStartAudio();
            window.removeEventListener('pointerdown', userGestureHandler);
            window.removeEventListener('keydown', userGestureHandler);
        };
        window.addEventListener('pointerdown', userGestureHandler, { once: true });
        window.addEventListener('keydown', userGestureHandler, { once: true });

        return () => {
            exitStoryMode();
            if (io) {
                io.disconnect();
                io = null;
            }
        };
    });

    onDestroy(() => {
        if (audioEl) {
            try { audioEl.pause(); } catch (e) {}
            audioEl = null;
        }
    });
</script>

<div class="slide-container" bind:this={containerEl}>
    <div class="top-left-audio">
        <div class="audio-indicator">
            <span class="dot" class:playing={isPlaying}></span>
            <span class="label">Narration</span>
        </div>
        <div class="speed-select compact">
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { narratorSpeed.set('normal'); speed = 'normal'; setTimeout(() => safeStartAudio(), 50); }}>
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { narratorSpeed.set('slow'); speed = 'slow'; setTimeout(() => safeStartAudio(), 50); }}>
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { narratorSpeed.set('fast'); speed = 'fast'; setTimeout(() => safeStartAudio(), 50); }}>
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    <h1 class="title">
        {$language === 'english' ? story.title.english : story.title.cebuano}
    </h1>

    <div class="image-wrapper">
        <img
            src={story.image}
            alt="Story Scene"
            class="story-image"
        />
    </div>

    <div class="story-text" transition:fade>
        {$language === 'english' ? story.text.english : story.text.cebuano}
    </div>

    <audio bind:this={audioEl} src={audioSrc} on:play={() => { isPlaying = true; }} on:pause={() => { isPlaying = false; }} on:ended={exitStoryMode}></audio>
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
        animation: fadeIn 0.5s ease-in-out;
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
        min-height: 0;  /* This is important for flex child to shrink */
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

    .top-left-audio {
        position: absolute;
        top: 12px;
        left: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
        z-index: 30;
        background: rgba(255,255,255,0.85);
        padding: 6px 8px;
        border-radius: 10px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
    }

    .audio-indicator { display:flex; align-items:center; gap:8px; }
    .audio-indicator .dot { width:10px; height:10px; border-radius:50%; background:#d1d5db; display:inline-block; }
    .audio-indicator .dot.playing { background: #10b981; box-shadow:0 0 6px #10b981; }
    .audio-indicator .label { font-size:12px; color:#374151; font-weight:600; }

    .speed-select.compact { display:flex; gap:6px; }
    .speed-select.compact .chip { display:inline-flex; align-items:center; gap:6px; padding:6px 8px; border-radius:999px; background:#f3f4f6; cursor:pointer; font-size:12px; }
    .speed-select.compact .chip.active { background:#e6fffa; border:1px solid #10b981; }
    .speed-select.compact input { display:none; }
    .speed-select.compact .txt { color:#111827; font-weight:600; }
</style>