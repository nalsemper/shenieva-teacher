<script>
  import { name } from "$lib/store/nameStore.js"; // Global store for name
  import { fade, slide, scale } from "svelte/transition"; // Svelte transitions
  import { goto } from '$app/navigation'; // SvelteKit navigation utility
  import Play from '../play/+page.svelte'; // Import Play component
  import Stats from '../stats/+page.svelte'; // Import Stats component
  import Profile from '../profile/+page.svelte'; // Import Profile component
  import Settings from '../settings/+page.svelte'; // Import Settings component

  let activeTab = "play"; // Track the active tab
  let showLogoutModal = false; // Control the visibility of the logout modal
  let hoveredTab = null; // Track the hovered tab

  function wiggleButton(tab) {
    activeTab = tab;
  }

  function setHoveredTab(tab) {
    hoveredTab = tab;
  }

  function clearHoveredTab() {
    hoveredTab = null;
  }

  function openLogoutModal() {
    showLogoutModal = true;
  }

  function confirmLogout() {
    showLogoutModal = false;
    goto('../'); // Redirect to parent directory
  }

  function cancelLogout() {
    showLogoutModal = false;
  }
</script>

<div class="min-h-screen flex flex-col items-center justify-start overflow-hidden bg-gray-100 relative" style="background: url('/src/assets/readville.jpg') no-repeat center center/cover;">
  <!-- Logo in Top-Left Corner -->
  <img
    src="/src/assets/shenievia.png"
    alt="Shenievia Logo"
    class="absolute top-4 left-0 z-20 animate-rubberband-double-loop drop-shadow-[0_0_4px_white] w-90 h-auto"
  />

  <!-- Subtle Animated Background -->
  <div class="absolute inset-0 bg-[url('/src/assets/kid-bg.gif')] bg-cover bg-center opacity-15 animate-pulse"></div>

  <!-- Floating Circle Buttons with Text and Underscore -->
  <div class="relative z-10 w-full p-4 flex justify-center gap-8 mt-8">
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl transition-all duration-300 hover:bg-lime-500 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
        class:bg-lime-300={activeTab !== "play"}
        class:bg-lime-400={activeTab === "play"}
        class:scale-125={activeTab === "play"}
        class:animate-wiggle={activeTab === "play"}
        class:shadow-[0_8px_16px_rgba(0,0,0,0.5),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]={activeTab === "play"}
        on:click={() => wiggleButton("play")}
        on:mouseenter={() => setHoveredTab("play")}
        on:mouseleave={clearHoveredTab}
      >
        🎮
      </button>
      <span
        class="mt-2 text-sm font-bold text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)] transition-all duration-300"
        class:scale-150={activeTab === "play" || hoveredTab === "play"}
        class:animate-wiggle={activeTab === "play"}
      >
        Play
      </span>
      {#if activeTab === "play"}
        <div class="w-8 h-1 bg-lime-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl transition-all duration-300 hover:bg-orange-500 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
        class:bg-orange-300={activeTab !== "dashboard"}
        class:bg-orange-400={activeTab === "dashboard"}
        class:scale-125={activeTab === "dashboard"}
        class:animate-wiggle={activeTab === "dashboard"}
        class:shadow-[0_8px_16px_rgba(0,0,0,0.5),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]={activeTab === "dashboard"}
        on:click={() => wiggleButton("dashboard")}
        on:mouseenter={() => setHoveredTab("dashboard")}
        on:mouseleave={clearHoveredTab}
      >
        📊
      </button>
      <span
        class="mt-2 text-sm font-bold text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)] transition-all duration-300"
        class:scale-150={activeTab === "dashboard" || hoveredTab === "dashboard"}
        class:animate-wiggle={activeTab === "dashboard"}
      >
        Stats
      </span>
      {#if activeTab === "dashboard"}
        <div class="w-8 h-1 bg-orange-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl transition-all duration-300 hover:bg-red-500 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
        class:bg-red-300={activeTab !== "profile"}
        class:bg-red-400={activeTab === "profile"}
        class:scale-125={activeTab === "profile"}
        class:animate-wiggle={activeTab === "profile"}
        class:shadow-[0_8px_16px_rgba(0,0,0,0.5),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]={activeTab === "profile"}
        on:click={() => wiggleButton("profile")}
        on:mouseenter={() => setHoveredTab("profile")}
        on:mouseleave={clearHoveredTab}
      >
        👤
      </button>
      <span
        class="mt-2 text-sm font-bold text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)] transition-all duration-300"
        class:scale-150={activeTab === "profile" || hoveredTab === "profile"}
        class:animate-wiggle={activeTab === "profile"}
      >
        Me
      </span>
      {#if activeTab === "profile"}
        <div class="w-8 h-1 bg-red-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl transition-all duration-300 hover:bg-gray-500 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
        class:bg-gray-300={activeTab !== "settings"}
        class:bg-gray-400={activeTab === "settings"}
        class:scale-125={activeTab === "settings"}
        class:animate-wiggle={activeTab === "settings"}
        class:shadow-[0_8px_16px_rgba(0,0,0,0.5),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]={activeTab === "settings"}
        on:click={() => wiggleButton("settings")}
        on:mouseenter={() => setHoveredTab("settings")}
        on:mouseleave={clearHoveredTab}
      >
        ⚙️
      </button>
      <span
        class="mt-2 text-sm font-bold text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)] transition-all duration-300"
        class:scale-150={activeTab === "settings" || hoveredTab === "settings"}
        class:animate-wiggle={activeTab === "settings"}
      >
        Fix
      </span>
      {#if activeTab === "settings"}
        <div class="w-8 h-1 bg-gray-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
  </div>

  <!-- Main Content Area with Imported Components -->
  <div class="relative z-10 w-3/4 max-w-lg mt-12 p-6 bg-white rounded-3xl shadow-xl" in:fade={{ duration: 600 }}>
    {#if activeTab === "play"}
      <Play />
    {:else if activeTab === "dashboard"}
      <Stats />
    {:else if activeTab === "profile"}
      <Profile />
    {:else if activeTab === "settings"}
      <Settings />
    {/if}
  </div>

  <!-- Logout Button -->
  <button
    title="Logout"
    class="absolute top-4 right-4 w-12 h-12 bg-orange-400 text-white text-xl font-bold rounded-full hover:bg-orange-500 transition-all duration-300 hover:scale-110 active:scale-100 z-30 flex items-center justify-center border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
    on:click={openLogoutModal}
  >
    ⏻
  </button>

  <!-- Logout Modal -->
  {#if showLogoutModal}
    <div
      class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-20"
      transition:fade={{ duration: 200 }}
    >
      <div
        class="bg-white p-6 rounded-3xl shadow-xl w-80 max-w-full"
        transition:scale={{ duration: 300, start: 0.9 }}
      >
        <h2 class="text-2xl font-bold text-purple-500 text-center mb-4 animate-bounce">
          Bye Bye? 👋
        </h2>
        <p class="text-lg text-gray-700 text-center mb-6">
          Do you want to leave the fun?
        </p>
        <div class="flex justify-center gap-4">
          <button
            class="w-20 h-12 bg-lime-400 text-white font-bold rounded-full hover:bg-lime-500 transition-all duration-300 hover:scale-110 active:scale-95 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
            on:click={confirmLogout}
          >
            Yes
          </button>
          <button
            class="w-20 h-12 bg-orange-400 text-white font-bold rounded-full hover:bg-orange-500 transition-all duration-300 hover:scale-110 active:scale-95 border-4 border-white shadow-[0_6px_12px_rgba(0,0,0,0.4),inset_0_4px_6px_rgba(255,255,255,0.7),inset_0_-4px_6px_rgba(0,0,0,0.3)]"
            on:click={cancelLogout}
          >
            No
          </button>
        </div>
      </div>
    </div>
  {/if}
</div>

<style>
  /* Custom Wiggle Animation for Buttons */
  @keyframes wiggle {
    0% { transform: rotate(0deg) scale(1.50); }
    25% { transform: rotate(5deg) scale(1.50); }
    50% { transform: rotate(-5deg) scale(1.50); }
    75% { transform: rotate(5deg) scale(1.50); }
    100% { transform: rotate(0deg) scale(1.50); }
  }
  .animate-wiggle {
    animation: wiggle 0.5s ease-in-out 1;
  }

  /* Rubberband Animation for Logo */
  @keyframes rubberband {
    0% { transform: scale(1); }
    30% { transform: scaleX(1.25) scaleY(0.75); }
    40% { transform: scaleX(0.75) scaleY(1.25); }
    60% { transform: scaleX(1.15) scaleY(0.85); }
    100% { transform: scale(1); }
  }
  .animate-rubberband-double-loop {
    animation: rubberband-double-loop 7s infinite;
  }
  @keyframes rubberband-double-loop {
    0% { transform: scale(1); } /* Start steady */
    14.29% { transform: scaleX(1.25) scaleY(0.75); } /* 1s: First rubberband start */
    19.05% { transform: scaleX(0.75) scaleY(1.25); }
    28.57% { transform: scaleX(1.15) scaleY(0.85); }
    28.58% { transform: scale(1); } /* Second rubberband end */
    57.15% { transform: scale(1); } /* Second rubberband end */
    100% { transform: scale(1); } /* Steady for remaining 5s */
  }
</style>