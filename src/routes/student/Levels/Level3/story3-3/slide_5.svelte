<script>
    import { onMount, onDestroy } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';
    import { language, narratorSpeed } from '\/store/story_lang_audio';
    import { fade } from 'svelte/transition';

    const slide = {
        text: {
            english: "Liloy looked everywhere. He checked behind the tree and even behind the flowerpot, but Lingling was nowhere to be found.",
            cebuano: "Nagpanikad si Liloy ug nangita siya bisan asa. Gipangsusi niya ang likod sa kahoy ug bisan ang likod sa paso, apan wala niya nakita si Lingling."
        },
        image: "/converted/assets/LEVEL_3/STORY_3/pic4.webp"
    };

    // Audio state
    let speed = $narratorSpeed;
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;

    // Audio source with speed variants
    $: audioSrc = (() => {
        const base = '/assets/audio/story-telling/Level_3/story_3';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        let filename = sp === 'normal' ? 'LL 4.mp3' : 'LL4.mp3';
        return `${base}/${sp}/slide_5/${filename}`;
    })();

    let playToken = 0;
    let _startTimer = null;

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
    let containerEl = null;
    /** @type {IntersectionObserver | null} */
    let io = null;

    // autoplay narration when component mounts and when slide becomes visible
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
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { speed = 'normal'; safeStartAudio(); }}>
                <input type="radio" name="speed5" bind:group={speed} value="normal" />
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { speed = 'slow'; safeStartAudio(); }}>
                <input type="radio" name="speed5" bind:group={speed} value="slow" />
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { speed = 'fast'; safeStartAudio(); }}>
                <input type="radio" name="speed5" bind:group={speed} value="fast" />
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    {#if slide.image}
        <div class="image-wrapper">
            <img src={slide.image} alt="Story Scene" class="story-image" />
        </div>
    {/if}
    <div class="story-text" transition:fade>
        {$language === 'english' ? slide.text.english : slide.text.cebuano}
    </div>

    <audio bind:this={audioEl} src={audioSrc} on:play={() => { isPlaying = true; }} on:pause={() => { isPlaying = false; }} on:ended={exitStoryMode}></audio>
</div>

<style>
    .slide-container { width: 100%; height: 100%; display:flex; flex-direction:column; justify-content:space-between; align-items:center; gap:1rem; padding:1rem; box-sizing:border-box; animation: fadeIn 1000ms ease-in forwards; position: relative; }
    .image-wrapper { flex:1; width:100%; display:flex; align-items:center; justify-content:center; min-height:0; }
    .story-image { max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; border-radius:0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .story-text { font-size:clamp(1rem,2vw,1.25rem); line-height:1.6; color:#1f2937; text-align:center; padding:0 1rem; margin:0; }
    @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }

    .top-left-audio {
        position: absolute;
        top: 12px;
        left: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
        z-index: 2100;
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
