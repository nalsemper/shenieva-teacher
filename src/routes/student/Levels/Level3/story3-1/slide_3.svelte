<script>
    import { onMount, onDestroy } from 'svelte';
    import { audioStore } from '$lib/store/audio_store';
    import { language, narratorSpeed } from '$lib/store/story_lang_audio';
    import { fade } from 'svelte/transition';

    const slide = {
        text: {
            english: "While making her bed, Tonya felt something different in her front tooth, so she decided to look in the mirror and check.",
            cebuano: "Samtang naghipos sa higdaanan, adunay lain nga gibati si Tonya sa iyang atubangan nga ngipon, busa nakahukom siya nga tan-awon sa salamin ug susihon."
        },
        image: "/converted/assets/LEVEL_3/STORY_1/PIC2.webp"
    };

    // Audio state
    let audioEl;
    let containerEl;
    let isPlaying = false;
    let observer;
    let storyModeActive = false;
    let playToken = 0;

    // Speed selector
    let speed = $narratorSpeed;

    // Audio source with speed variants (slow has different filename)
    $: audioSrc = (() => {
        const base = '/assets/audio/story-telling/Level_3/story_1';
        const sp = speed === 'slow' ? 'slow' : (speed === 'fast' ? 'fast' : 'normal');
        let filename;
        if (sp === 'slow') {
            filename = 'T2.mp3';
        } else if (sp === 'fast') {
            filename = 'TT2.mp3';
        } else {
            filename = 'TT 2.mp3';
        }
        return `${base}/${sp}/slide_3/${filename}`;
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

    // watch for speed changes to auto-play new narration
    $: if (audioEl && speed && audioSrc) {
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
    <div class="top-left-audio">
        <div class="audio-indicator">
            <span class="dot" class:playing={isPlaying}></span>
            <span class="label">Narration</span>
        </div>
        <div class="speed-select compact">
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { narratorSpeed.set('normal'); speed = 'normal'; }}><span class="txt">Normal</span></label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { narratorSpeed.set('slow'); speed = 'slow'; }}><span class="txt">Slow</span></label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { narratorSpeed.set('fast'); speed = 'fast'; }}><span class="txt">Fast</span></label>
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

    <audio 
        bind:this={audioEl} 
        on:play={() => isPlaying = true}
        on:pause={() => isPlaying = false}
        on:ended={exitStoryMode}
    ></audio>
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
