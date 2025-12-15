<script>
    import { fade } from 'svelte/transition';
    import { language, narratorSpeed } from "$lib/store/story_lang_audio";
    import { onMount, onDestroy } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';

    let speed = $narratorSpeed; // slow | normal | fast
    let isPlaying = false;
    /** @type {HTMLAudioElement | null} */
    let audioEl = null;
    let containerEl = null;
    /** @type {IntersectionObserver | null} */
    let io = null;

    const slide = {
        text: {
            english: "She apologized to Lena and said, \"I'm sorry, I don't have any small bill. I'll come back later to pay you.\" Lena smiled and nodded.",
            cebuano: "Miingon si Maria nga, \"Pasayloa ko, wala koy gagmay nga kwarta. Mubalik lang ko unya para mubayad.\" Si Lena mipahiyom ug nitando."
        },
    image: "/converted/assets/LEVEL_1/STORY_1/PIC4.webp"
    };

    // playlist per speed
    // fast: M4, Maria 1 (M4 pauses at 4s, plays Maria 1, then resumes M4)
    // normal/slow: M4, Maria 1, M5
    $: playlist = (() => {
        const base = '/assets/audio/story-telling/Level_1/story_1';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        
        if (speed === 'fast') {
            return [
                encodeURI(`${base}/${sp}/slide_4/M4.mp3`),
                encodeURI(`${base}/${sp}/slide_4/Maria 1.mp3`)
            ];
        } else {
            return [
                encodeURI(`${base}/${sp}/slide_4/M4.mp3`),
                encodeURI(`${base}/${sp}/slide_4/Maria 1.mp3`),
                encodeURI(`${base}/${sp}/slide_4/M5.mp3`)
            ];
        }
    })();

    let playToken = 0;
    let _startTimer = null;
    let playlistIndex = 0;
    let m4PausedTime = 0; // Store pause position for M4.mp3 in fast mode
    let timeUpdateListener = null;

    function safeStartList(list){
        if (!audioEl) return;
        if (_startTimer) { clearTimeout(_startTimer); _startTimer = null; }
        playToken++;
        const token = playToken;
        playlistIndex = 0;
        m4PausedTime = 0;
        enterStoryMode();
        _startTimer = setTimeout(()=>{
            if (!audioEl) { _startTimer = null; return; }
            if (token !== playToken) { _startTimer = null; return; }
            
            // Use current playlist, not the passed list parameter
            const currentPlaylist = list || playlist;
            audioEl.src = currentPlaylist[playlistIndex];
            
            // For fast mode, set up time listener to pause M4 at 4 seconds
            if (speed === 'fast' && playlistIndex === 0) {
                setupM4TimeListener();
            }
            
            audioEl.play().then(()=>{ isPlaying = true; }).catch(()=>{ isPlaying = false; });
            _startTimer = null;
        }, 150);
    }

    function setupM4TimeListener() {
        if (!audioEl) return;
        
        // Remove previous listener if exists
        if (timeUpdateListener) {
            audioEl.removeEventListener('timeupdate', timeUpdateListener);
        }
        
        timeUpdateListener = () => {
            if (audioEl && audioEl.currentTime >= 4.0 && playlistIndex === 0) {
                m4PausedTime = audioEl.currentTime;
                audioEl.pause();
                audioEl.removeEventListener('timeupdate', timeUpdateListener);
                timeUpdateListener = null;
                
                // Auto-advance to Maria 1.mp3
                playlistIndex = 1;
                audioEl.src = playlist[1];
                audioEl.play().then(()=>{ isPlaying = true; }).catch(()=>{ isPlaying = false; });
            }
        };
        
        audioEl.addEventListener('timeupdate', timeUpdateListener);
    }

    function handleAudioEnd(){
        // Special handling for fast mode: after Maria 1, resume M4 from paused position
        if (speed === 'fast' && playlistIndex === 1 && m4PausedTime > 0) {
            playlistIndex = 2; // Mark as on "virtual" third track
            audioEl.src = playlist[0]; // Back to M4.mp3
            audioEl.currentTime = m4PausedTime;
            audioEl.play().then(()=>{ isPlaying = true; }).catch(()=>{ isPlaying = false; });
            m4PausedTime = 0; // Reset for next playback
            return;
        }
        
        playlistIndex++;
        if (playlistIndex < playlist.length){
            audioEl.src = playlist[playlistIndex];
            audioEl.play().then(()=>{ isPlaying = true; }).catch(()=>{ isPlaying = false; });
        } else {
            isPlaying = false;
            exitStoryMode();
        }
    }

    function togglePlay(){ if (!audioEl) return; if (isPlaying){ audioEl.pause(); isPlaying = false; } else { safeStartList(playlist); } }

    // story mode BGM ducking
    let storyModeActive = false;
    let _savedBgmVolume = null;
    function enterStoryMode(){ if (!storyModeActive){ try{ audioStore.init(); }catch{} try{ _savedBgmVolume = audioStore.getVolume(); }catch{ _savedBgmVolume = 0.7; } try{ audioStore.lockVolume(0.09);}catch(e){ audioStore.setVolume(0.09,true); } storyModeActive = true; } }
    function exitStoryMode(){ if (storyModeActive){ try{ audioStore.unlockVolume(); }catch(e){ if (typeof _savedBgmVolume === 'number') audioStore.setVolume(_savedBgmVolume,true); } _savedBgmVolume = null; storyModeActive = false; } }

    onMount(()=>{
        setTimeout(()=> safeStartList(playlist), 80);
        if (typeof IntersectionObserver !== 'undefined'){
            io = new IntersectionObserver(entries=>{ entries.forEach(entry=>{ if (entry.isIntersecting && entry.intersectionRatio>0.5) safeStartList(playlist); }); }, { threshold: [0.5] });
            if (containerEl) io.observe(containerEl);
        }
        const userGesture = ()=>{ if (!isPlaying && playToken === 0) { safeStartList(playlist); } window.removeEventListener('pointerdown', userGesture); window.removeEventListener('keydown', userGesture); };
        window.addEventListener('pointerdown', userGesture, { once: true });
        window.addEventListener('keydown', userGesture, { once: true });
    });

    onDestroy(()=>{
        if (io){ try{ io.disconnect(); }catch{} io = null; }
        if (timeUpdateListener && audioEl) {
            try { audioEl.removeEventListener('timeupdate', timeUpdateListener); } catch {}
            timeUpdateListener = null;
        }
        if (audioEl){ try{ audioEl.pause(); }catch{} audioEl = null; }
        exitStoryMode();
    });
</script>

<div class="slide-container" bind:this={containerEl}>
    <div class="top-left-audio">
        <div class="audio-indicator">
            <span class="dot" class:playing={isPlaying}></span>
            <span class="label">Narration</span>
        </div>
        <div class="speed-select compact">
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { narratorSpeed.set('normal'); speed = 'normal'; setTimeout(() => safeStartList(playlist), 50); }}>
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { narratorSpeed.set('slow'); speed = 'slow'; setTimeout(() => safeStartList(playlist), 50); }}>
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { narratorSpeed.set('fast'); speed = 'fast'; setTimeout(() => safeStartList(playlist), 50); }}>
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

    <audio bind:this={audioEl} on:ended={handleAudioEnd} on:pause={()=>{ isPlaying = false; }}></audio>
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
        animation: fadeIn 1000ms ease-in forwards;
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .story-text {
        font-size: clamp(1rem, 2vw, 1.25rem);
        line-height: 1.6;
        color: #1f2937;
        text-align: center;
        padding: 0 1rem;
        margin: 0;
        animation: textFadeIn 1000ms ease-in forwards;
    }

    /* Audio control UI */
    .top-left-audio { position: absolute; top: 12px; left: 12px; display:flex; flex-direction:column; gap:8px; align-items:flex-start; z-index:30; background: rgba(255,255,255,0.85); padding:6px 8px; border-radius:10px; box-shadow:0 6px 14px rgba(0,0,0,0.08); }
    .audio-indicator { display:flex; align-items:center; gap:8px; }
    .audio-indicator .dot { width:10px; height:10px; border-radius:50%; background:#d1d5db; display:inline-block; }
    .audio-indicator .dot.playing { background:#10b981; box-shadow:0 0 6px #10b981; }
    .audio-indicator .label { font-size:12px; color:#374151; font-weight:600; }
    .speed-select.compact { display:flex; gap:6px; }
    .speed-select.compact .chip { display:inline-flex; align-items:center; gap:6px; padding:6px 8px; border-radius:999px; background:#f3f4f6; cursor:pointer; font-size:12px; }
    .speed-select.compact .chip.active { background:#e6fffa; border:1px solid #10b981; }
    .speed-select.compact input { display:none; }
    .speed-select.compact .txt { color:#111827; font-weight:600; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes textFadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
