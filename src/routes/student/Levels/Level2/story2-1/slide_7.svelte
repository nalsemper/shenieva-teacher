<script>
    import { fade } from 'svelte/transition';
    import { onMount, onDestroy } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';
    import { language, narratorSpeed } from '$lib/store/story_lang_audio';

    const slide = {
        english: { text: "Hector's mother brought him to the hospital. The doctor said, \"Hector's weight is above average for his age right now. He needs to practice eating healthy foods like vegetables and fruits, avoid eating too many sweets like chocolate and should start exercising.\" Hector listened to what the doctor said." },
        cebuano: { text: "Gidala si Hector ang iyang inahan sa balay-tambalanan. Miingon ang doktor, \"Ang timbang ni Hector labaw sa insaktong timbang para iyang edad karon. Kinahanglan siya magkat-on sa pagkaon ug mga makahimsog nga pagkaon sama sa mga gulay ug prutas, likayan ang pagkaon ug daghang tam-is sama sa tsokolate, ug magsugod sa pag-ehersisyo.\" Naminaw si Hector sa mga pulong sa doktor." },
        image: '/converted/assets/LEVEL_2/STORY_1/PIC5.webp'
    };    let currentLanguage;
    language.subscribe(v => currentLanguage = v);
    $: currentText = slide[currentLanguage].text;

    // Audio state
    let audioEl;
    let containerEl;
    let isPlaying = false;
    let observer;
    let storyModeActive = false;
    let _savedBgmVolume = null;
    let playToken = 0;

    // Speed and playlist
    let speed = $narratorSpeed;
    let playlistIndex = 0;

    // Playlist with 4 files for fast, 3 files for normal/slow
    $: playlist = (() => {
        const base = '/assets/audio/story-telling/Level_2/story_1';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        
        // Fast folder has 4 files: HH7, HH8, Doctor, HH9
        // Normal/Slow have 3 files: HH7, Doctor, HH8
        if (sp === 'fast') {
            return [
                `${base}/${sp}/slide_7/HH7.mp3`,
                `${base}/${sp}/slide_7/HH8.mp3`,
                `${base}/${sp}/slide_7/Doctor.mp3`,
                `${base}/${sp}/slide_7/HH9.mp3`
            ];
        } else {
            return [
                `${base}/${sp}/slide_7/HH7.mp3`,
                `${base}/${sp}/slide_7/Doctor.mp3`,
                `${base}/${sp}/slide_7/HH8.mp3`
            ];
        }
    })();

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

    function safeStartList(list) {
        if (!audioEl || !list || list.length === 0) return;
        playlistIndex = 0;
        enterStoryMode();
        const token = ++playToken;
        audioEl.pause();
        audioEl.src = list[0];
        audioEl.load();
        setTimeout(() => {
            if (token !== playToken) return;
            audioEl.play().catch(e => console.warn('[slide_7] Play blocked:', e));
        }, 150);
    }

    function handleAudioEnd() {
        playlistIndex++;
        if (playlistIndex < playlist.length) {
            audioEl.src = playlist[playlistIndex];
            audioEl.load();
            audioEl.play().catch(e => console.warn('[slide_7] Next track play blocked:', e));
        } else {
            exitStoryMode();
        }
    }

    onMount(() => {
        if (typeof IntersectionObserver !== 'undefined' && containerEl) {
            observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                            safeStartList(playlist);
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
                    safeStartList(playlist);
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
    <!-- Narrator UI -->
    <div class="top-left-audio">
        <div class="audio-indicator">
            <span class="dot" class:playing={isPlaying}></span>
            <span class="label">Narration</span>
        </div>
        <div class="speed-select compact">
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { speed = 'normal'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed7" bind:group={speed} value="normal" />
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { speed = 'slow'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed7" bind:group={speed} value="slow" />
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { speed = 'fast'; setTimeout(() => safeStartList(playlist), 50); }}>
                <input type="radio" name="speed7" bind:group={speed} value="fast" />
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    <div class="image-wrapper"><img src={slide.image} alt="" class="story-image"/></div>
    <div class="story-text">{currentText}</div>

    <audio 
        bind:this={audioEl} 
        on:play={() => isPlaying = true}
        on:pause={() => isPlaying = false}
        on:ended={handleAudioEnd}
    ></audio>
</div>

<style>
    .slide-container { width: 100%; height: 100%; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:1rem; padding:1rem; box-sizing:border-box; position: relative; }
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
