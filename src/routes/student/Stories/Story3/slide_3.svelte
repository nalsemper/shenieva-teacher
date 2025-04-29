<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    const dispatch = createEventDispatcher();

    const slide = {
        text: "Choose vendor. 🌟",
    };

    const vendors = [
        { id: 1, image: "/src/assets/story1/vendor1.png", nextPath: "/src/routes/student/Stories/Story1/store1/slide_1.svelte" },
        { id: 2, image: "/src/assets/story1/vendor2.png", nextPath: "/src/routes/student/Stories/Story1/store2/slide_1.svelte" },
        { id: 3, image: "/src/assets/story1/vendor3.png", nextPath: "/src/routes/student/Stories/Story1/store3/slide_1.svelte" }
    ];

    function handleVendorSelect(vendorId: number): void {
        const selectedVendor = vendors.find(v => v.id === vendorId);
        if (selectedVendor) {
            dispatch('vendorSelected', { vendorId, nextPath: selectedVendor.nextPath });
        }
    }
</script>

<div class="flex flex-col justify-center items-center text-center slide">
    {#if slide.image}
        <div class="image-container">
            <img
                src={slide.image}
                alt="Story Scene"
                class="block mx-auto rounded-[2vw] shadow-lg"
            />
        </div>
    {/if}
    
    <p class="text-[4vw] md:text-2xl text-gray-800 font-semibold text-fade">
        {slide.text}
    </p>

    <div class="vendor-container">
        {#each vendors as vendor}
            <button 
                class="vendor-button"
                on:click={() => handleVendorSelect(vendor.id)}
            >
                <img
                    src={vendor.image}
                    alt={`Vendor ${vendor.id}`}
                    class="vendor-image"
                />
                <span class="vendor-label">Vendor {vendor.id}</span>
            </button>
        {/each}
    </div>
</div>
<style>
    .slide {
        animation: fadeIn 1000ms ease-in forwards;
        will-change: opacity;
    }

    .image-container {
        width: 80vw;
        height: 80vh;
        max-width: 800px;
        max-height: 400px;
        margin-bottom: 2vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .text-fade {
        white-space: pre-wrap;
        overflow-wrap: break-word;
        max-width: 80%;
        animation: textFadeIn 1000ms ease-in forwards;
        will-change: opacity;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transform: translateZ(0);
    }

    .vendor-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        margin-top: 2rem;
        width: 80%;
        max-width: 800px;
    }

    .vendor-button {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 200px;
    }

    .vendor-button:hover {
        transform: scale(1.05);
        border-color: #4299e1;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .vendor-image {
        width: 150px;
        height: 150px;
        object-fit: contain;
        margin-bottom: 0.5rem;
    }

    .vendor-label {
        color: #2d3748;
        font-size: 1.1rem;
        font-weight: 500;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes textFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    :global(.slide) {
        animation: fadeOut 1000ms ease-out forwards;
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>