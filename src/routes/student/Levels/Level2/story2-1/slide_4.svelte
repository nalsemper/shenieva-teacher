<script>
    import { fade } from "svelte/transition";
    import { language, narratorSpeed } from "$lib/store/story_lang_audio";
    import { onDestroy, onMount } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';

    const slide = {
        english: { text: "When he arrived at his mother's market stall, his mother noticed that Hector was breathing with difficulty and his face turned pale." },
        cebuano: { text: "Pag-abot niya sa tindahan sa iyang inahan sa merkado, namatikdan sa iyang inahan nga lisod ang iyang pagginhawa ug luspad ang iyang nawong." },
        image: '/converted/assets/LEVEL_2/STORY_1/PIC3.webp'
    };

    let currentLanguage;
    language.subscribe(v => currentLanguage = v);
    $: currentText = slide[currentLanguage].text;

    // Audio state
    let speed = $narratorSpeed;
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;

    // compute audio src based on speed
    $: audioSrc = (() => {
        const base = '/assets/audio/story-telling/Level_2/story_1';
        const speedFolder = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        return `${base}/${speedFolder}/slide_4/HH4.mp3`;
    })();

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
        enterStoryMode();
        safeStart(audioSrc);
    }

    // autoplay narration when component mounts and when slide becomes visible
    onMount(() => {
        setTimeout(() => startNarration(), 80);

        if (typeof IntersectionObserver !== 'undefined') {
            io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                        startNarration();
                    }
                });
            }, { threshold: [0, 0.5, 1] });

            if (containerEl) {
                io.observe(containerEl);
            }
        }

        // If autoplay is blocked, listen for first user gesture
        const userGestureHandler = () => {
            startNarration();
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
</script>

<div class="slide-container" bind:this={containerEl}>
    <div class="top-left-audio">
        <div class="audio-indicator">
            <span class="dot" class:playing={isPlaying}></span>
            <span class="label">Narration</span>
        </div>
        <div class="speed-select compact">
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { speed = 'normal'; startNarration(); }}>
                <input type="radio" name="speed4" bind:group={speed} value="normal" />
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { speed = 'slow'; startNarration(); }}>
                <input type="radio" name="speed4" bind:group={speed} value="slow" />
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { speed = 'fast'; startNarration(); }}>
                <input type="radio" name="speed4" bind:group={speed} value="fast" />
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    <div class="image-wrapper"><img src={slide.image} alt="" class="story-image"/></div>
    <div class="story-text">{currentText}</div>

    <audio bind:this={audioEl} on:ended={() => { isPlaying = false; exitStoryMode(); }} on:pause={() => { isPlaying = false; }}></audio>
</div>

<style>
    .slide-container { width: 100%; height: 100%; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:1rem; padding:1rem; box-sizing:border-box; }
    .image-wrapper { flex:1; width:100%; display:flex; align-items:center; justify-content:center; min-height:0; }
    .story-image { max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; border-radius:0.5rem; }
    .story-text { font-size:clamp(1rem,2vw,1.25rem); line-height:1.6; color:#1f2937; text-align:center; padding:0 1rem; margin:0; }

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