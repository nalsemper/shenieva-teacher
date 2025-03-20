<script>
  import { name } from "$lib/store/nameStore.js"; // Global store for name
  import { fade, slide, scale } from "svelte/transition"; // Svelte transitions
  import { goto } from '$app/navigation'; // SvelteKit navigation utility

  let activeTab = "play"; // Track the active tab
  let showLogoutModal = false; // Control the visibility of the logout modal

  function wiggleButton(tab) {
    activeTab = tab;
  }

  // Show the logout modal
  function openLogoutModal() {
    showLogoutModal = true;
  }

  // Handle logout confirmation
  function confirmLogout() {
    showLogoutModal = false;
    goto('../'); // Redirect to parent directory
  }

  // Cancel logout
  function cancelLogout() {
    showLogoutModal = false;
  }
</script>

<div class="min-h-screen flex flex-col items-center justify-start overflow-hidden bg-gray-100">
  <!-- Subtle Animated Background -->
  <div class="absolute inset-0 bg-[url('/src/assets/kid-bg.gif')] bg-cover bg-center opacity-15 animate-pulse"></div>

  <!-- Floating Circle Buttons with Text and Underscore -->
  <div class="relative z-10 w-full p-4 flex justify-center gap-8 mt-8">
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg transition-all duration-300 hover:bg-lime-500"
        class:bg-lime-300={activeTab !== "play"}
        class:bg-lime-400={activeTab === "play"}
        class:scale-125={activeTab === "play"}
        class:animate-wiggle={activeTab === "play"}
        class:shadow-[0_0_15px_5px_rgba(255,255,255,0.8)]={activeTab === "play"}
        on:click={() => wiggleButton("play")}
      >
        🎮
      </button>
      <span class="mt-2 text-sm font-bold text-gray-700">Play</span>
      {#if activeTab === "play"}
        <div class="w-8 h-1 bg-lime-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg transition-all duration-300 hover:bg-orange-500"
        class:bg-orange-300={activeTab !== "dashboard"}
        class:bg-orange-400={activeTab === "dashboard"}
        class:scale-125={activeTab === "dashboard"}
        class:animate-wiggle={activeTab === "dashboard"}
        class:shadow-[0_0_15px_5px_rgba(255,255,255,0.8)]={activeTab === "dashboard"}
        on:click={() => wiggleButton("dashboard")}
      >
        📊
      </button>
      <span class="mt-2 text-sm font-bold text-gray-700">Stats</span>
      {#if activeTab === "dashboard"}
        <div class="w-8 h-1 bg-orange-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg transition-all duration-300 hover:bg-red-500"
        class:bg-red-300={activeTab !== "profile"}
        class:bg-red-400={activeTab === "profile"}
        class:scale-125={activeTab === "profile"}
        class:animate-wiggle={activeTab === "profile"}
        class:shadow-[0_0_15px_5px_rgba(255,255,255,0.8)]={activeTab === "profile"}
        on:click={() => wiggleButton("profile")}
      >
        👤
      </button>
      <span class="mt-2 text-sm font-bold text-gray-700">Me</span>
      {#if activeTab === "profile"}
        <div class="w-8 h-1 bg-red-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
    <div class="flex flex-col items-center">
      <button
        class="w-16 h-16 rounded-full flex items-center justify-center text-2xl shadow-lg transition-all duration-300 hover:bg-gray-500"
        class:bg-gray-300={activeTab !== "settings"}
        class:bg-gray-400={activeTab === "settings"}
        class:scale-125={activeTab === "settings"}
        class:animate-wiggle={activeTab === "settings"}
        class:shadow-[0_0_15px_5px_rgba(255,255,255,0.8)]={activeTab === "settings"}
        on:click={() => wiggleButton("settings")}
      >
        ⚙️
      </button>
      <span class="mt-2 text-sm font-bold text-gray-700">Fix</span>
      {#if activeTab === "settings"}
        <div class="w-8 h-1 bg-gray-400 mt-1 rounded transition-all duration-300" in:slide={{ duration: 200 }}></div>
      {/if}
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="relative z-10 w-3/4 max-w-lg mt-12 p-6 bg-white rounded-3xl shadow-xl" in:fade={{ duration: 600 }}>
    {#if activeTab === "play"}
      <div in:slide={{ duration: 400 }}>
        <h1 class="text-4xl font-bold text-lime-500 text-center mb-4 animate-bounce">
          Hi, {$name}! 🌟
        </h1>
        <p class="text-xl text-gray-700 text-center">
          <span class="text-orange-500">Play Time!</span> Games and fun stuff await!
        </p>
      </div>
    {:else if activeTab === "dashboard"}
      <div in:slide={{ duration: 400 }}>
        <h1 class="text-4xl font-bold text-orange-500 text-center mb-4 animate-bounce">
          Your Stats! 📈
        </h1>
        <p class="text-xl text-gray-700 text-center">
          See how great you’re doing!
        </p>
      </div>
    {:else if activeTab === "profile"}
      <div in:slide={{ duration: 400 }}>
        <h1 class="text-4xl font-bold text-red-500 text-center mb-4 animate-bounce">
          All About You! 🌈
        </h1>
        <p class="text-xl text-gray-700 text-center">
          Look at your cool stuff!
        </p>
      </div>
    {:else if activeTab === "settings"}
      <div in:slide={{ duration: 400 }}>
        <h1 class="text-4xl font-bold text-gray-500 text-center mb-4 animate-bounce">
          Change It Up! 🎨
        </h1>
        <p class="text-xl text-gray-700 text-center">
          Make it your way!
        </p>
      </div>
    {/if}
  </div>

  <!-- Logout Button -->
  <button
  title="Logout"
  class="absolute top-4 right-4 w-12 h-12 bg-orange-400 text-white text-xl font-bold rounded-full shadow-md hover:bg-orange-500 transition-all duration-300 hover:scale-110 active:scale-100 z-30 flex items-center justify-center"
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
            class="w-20 h-12 bg-lime-400 text-white font-bold rounded-full shadow-md hover:bg-lime-500 transition-all duration-300 hover:scale-110 active:scale-95"
            on:click={confirmLogout}
          >
            Yes
          </button>
          <button
            class="w-20 h-12 bg-orange-400 text-white font-bold rounded-full shadow-md hover:bg-orange-500 transition-all duration-300 hover:scale-110 active:scale-95"
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
  /* Custom Wiggle Animation for Tailwind */
  @keyframes wiggle {
    0% { transform: rotate(0deg) scale(1.25); }
    25% { transform: rotate(5deg) scale(1.25); }
    50% { transform: rotate(-5deg) scale(1.25); }
    75% { transform: rotate(5deg) scale(1.25); }
    100% { transform: rotate(0deg) scale(1.25); }
  }
  .animate-wiggle {
    animation: wiggle 0.5s ease-in-out 1;
  }
</style>