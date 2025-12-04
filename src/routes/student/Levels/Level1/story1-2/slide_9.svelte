<script>
    import { fade } from 'svelte/transition';
    import { onMount, onDestroy } from 'svelte';
    import { language, narratorSpeed } from "$lib/store/story_lang_audio";

    let speed = $narratorSpeed;
    let isPlaying = false;
    let audioEl;
    let containerEl;

    const slide = {
        text: {
            english: "From that day on, she realized that it is important to avoid eating too much  sweets and that it is important to brush her teeth afterward. Candice maintains the good health of her teeth and spreads the lesson to her friends.",
            cebuano: "Niduol si Maria ug niingon, \"Daghang salamat sa pagsalig nako, Lena. Ania na ang akong bayad.\" Nitando si Lena ug miingon, \"Walay problema gyud. Nalipay ako nga nakabayad ka.\" Si Maria mibalos ug pasalamat pag-usab, ug si Lena nabati ang kalipay nga nakasalig siya sa iyang kustomer kay nasabtan niya nga ang pagsalig importante kaayo sa negosyo."
        },
    image: "/converted/assets/LEVEL_1/STORY_2/PIC6.webp"
    };

    function safeStart() {
        if (!audioEl) return;
        audioEl.pause();
        audioEl.currentTime = 0;
        const fileName = (speed === 'fast') ? 'C6.mp3' : 'C7.mp3';
        audioEl.src = `/assets/audio/story-telling/Level_1/story_2/${speed}/slide_9/${fileName}`;
        audioEl.play();
    }

    function handlePlay() {
        isPlaying = true;
    }

    function handlePause() {
        isPlaying = false;
    }

    function handleEnd() {
        isPlaying = false;
    }

    onMount(() => {
        safeStart();
    });

    onDestroy(() => {
        if (audioEl) {
            audioEl.pause();
            audioEl.currentTime = 0;
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
            <label class="chip {speed === 'normal' ? 'active' : ''}" on:click={() => { speed = 'normal'; safeStart(); }}>
                <input type="radio" name="speed9" bind:group={speed} value="normal" />
                <span class="txt">Normal</span>
            </label>
            <label class="chip {speed === 'slow' ? 'active' : ''}" on:click={() => { speed = 'slow'; safeStart(); }}>
                <input type="radio" name="speed9" bind:group={speed} value="slow" />
                <span class="txt">Slow</span>
            </label>
            <label class="chip {speed === 'fast' ? 'active' : ''}" on:click={() => { speed = 'fast'; safeStart(); }}>
                <input type="radio" name="speed9" bind:group={speed} value="fast" />
                <span class="txt">Fast</span>
            </label>
        </div>
    </div>

    {#if slide.image}
        <div class="image-wrapper">
            <img
                src={slide.image}
                alt="Story Scene"
                class="story-image"
            />
        </div>
    {/if}
    <div class="story-text" transition:fade>
        {$language === 'english' ? slide.text.english : slide.text.cebuano}
    </div>
</div>

<audio
    bind:this={audioEl}
    on:play={handlePlay}
    on:pause={handlePause}
    on:ended={handleEnd}
></audio>

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
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
