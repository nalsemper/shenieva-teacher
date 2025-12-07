<script>
    import { fade } from "svelte/transition";
    import { language, narratorSpeed } from "$lib/store/story_lang_audio";
    import { onDestroy, onMount } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';

    const slide = {
        english: {
            text: "On one sunny day, Hector and his friend were on their way to the market to assist their parents. \"Hector! Hector! Hurry up!\" his friend called out Hector as he frequently missed out on the walk.",
        },
        cebuano: {
            text: "Sa usa ka mainit nga adlaw, si Hector ug ang iyang higala naglakaw padulong sa merkado aron tabangan ang ilang mga ginikanan. \"Hector! Hector! Dali na!\", tawag sa iyang higala kay pirmi man siya malangay sa paglakaw.",
        },
        image: '/converted/assets/LEVEL_2/STORY_1/PIC2.webp'
    };

    let currentLanguage;
    language.subscribe(v => currentLanguage = v);
    $: currentText = slide[currentLanguage].text;

    // Audio state
    let speed = $narratorSpeed; // 'slow' | 'normal' | 'fast'
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;

    // playlist: HH2, Friend, HH3
    $: playlist = (() => {
        const base = '/assets/audio/story-telling/Level_2/story_1';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        return [
            `${base}/${sp}/slide_3/HH2.mp3`,
            `${base}/${sp}/slide_3/Friend.mp3`,
            `${base}/${sp}/slide_3/HH3.mp3`
        ];
    })();

    let playToken = 0;
    let _startTimer = null;
    let playlistIndex = 0;

    function safeStartList(list) {
        if (!audioEl) {
            console.log('[slide_3] safeStartList called but audioEl is null');
            return;
        }
        console.log('[slide_3] safeStartList called with list:', list);
        if (_startTimer) { clearTimeout(_startTimer); _startTimer = null; }
        playToken++;
        const token = playToken;
        playlistIndex = 0;
        enterStoryMode();
        _startTimer = setTimeout(() => {
            if (!audioEl) { _startTimer = null; return; }
            if (token !== playToken) { _startTimer = null; return; }
            console.log('[slide_3] Starting audio:', list[playlistIndex]);
            audioEl.src = list[playlistIndex];
            audioEl.play().then(() => { 
                console.log('[slide_3] Audio started successfully');
                isPlaying = true; 
            }).catch((err) => { 
                console.error('[slide_3] Audio play failed:', err);
                isPlaying = false; 
            });
            _startTimer = null;
        }, 150);
    }

    function handleAudioEnd() {
        console.log('[slide_3] Audio ended, playlistIndex:', playlistIndex, 'playlist length:', playlist.length);
        playlistIndex++;
        if (playlistIndex < playlist.length) {
            console.log('[slide_3] Playing next:', playlist[playlistIndex]);
            audioEl.src = playlist[playlistIndex];
            audioEl.play().then(() => { isPlaying = true; }).catch((err) => { 
                console.error('[slide_3] Next audio play failed:', err);
                isPlaying = false; 
            });
        } else {
            console.log('[slide_3] Playlist finished');
            isPlaying = false;
            exitStoryMode();
        }
    }

    function togglePlay() {
        if (!audioEl) return;
        if (isPlaying) {
            audioEl.pause();
            isPlaying = false;
        } else {
            safeStartList(playlist);
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
        setTimeout(() => safeStartList(playlist), 80);

        if (typeof IntersectionObserver !== 'undefined') {
            io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                        safeStartList(playlist);
                    }
                });
            }, { threshold: [0, 0.5, 1] });

            if (containerEl) {
                io.observe(containerEl);
            }
        }

        // If autoplay is blocked, listen for first user gesture and then start narration
        const userGestureHandler = () => {
            safeStartList(playlist);
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
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { speed = 'normal'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed3" bind:group={speed} value="normal" />
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { speed = 'slow'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed3" bind:group={speed} value="slow" />
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { speed = 'fast'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed3" bind:group={speed} value="fast" />
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    <div class="image-wrapper"><img src={slide.image} alt="" class="story-image"/></div>
    <div class="story-text">{currentText}</div>

    <audio bind:this={audioEl} on:ended={handleAudioEnd} on:pause={() => { isPlaying = false; }}></audio>
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
