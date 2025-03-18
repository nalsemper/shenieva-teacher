<script>
  import { name } from "$lib/store/nameStore.js"; // Global store for name
  import { Tabs, TabItem } from "flowbite-svelte";
  import { fade, slide } from "svelte/transition"; // Svelte transitions
</script>

<div class="min-h-screen flex items-center justify-center overflow-hidden">
  <!-- Subtle Animated Background -->
  <div class="absolute inset-0 bg-[url('/src/assets/school-bg.gif')] bg-cover bg-center"></div>

  <!-- Main Content Container -->
  <div
    class="absolute z-10 w-full p-2 top-0 transition-all duration-500 hover:shadow-2xl"
    in:fade={{ duration: 600 }}
  >

    <!-- Tabs Section -->
    <Tabs
      class="rounded-2xl bg-gray-50 border-2 border-lime-300 shadow-md overflow-hidden"
      contentClass="p-6"
    >
      <!-- Play Tab -->
      <TabItem open title="🎮 Play">
        <!-- Greeting Header -->
    <h1
    class="text-5xl font-extrabold text-orange-500 text-center mb-6 animate-bounce-slow"
    in:slide={{ duration: 500 }}
  >
    Hey, {$name}! 🎉
  </h1>
        <div
          class="p-6 bg-lime-100 rounded-xl shadow-md transition-transform duration-300 hover:scale-102"
          in:slide={{ duration: 400 }}
        >
          <p class="text-2xl font-semibold text-gray-800">
            <span class="text-orange-500">Let’s Play!</span> Explore fun games and learn cool stuff!
          </p>
        </div>
      </TabItem>

      <!-- Dashboard Tab -->
      <TabItem title="📊 Dashboard">
        <div
          class="p-6 bg-orange-100 rounded-xl shadow-md transition-transform duration-300 hover:scale-102"
          in:slide={{ duration: 400 }}
        >
          <p class="text-2xl font-semibold text-gray-800">
            <span class="text-lime-500">Dashboard:</span> See your stats and progress!
          </p>
        </div>
      </TabItem>

      <!-- Profile Tab -->
      <TabItem title="👤 Profile">
        <div
          class="p-6 bg-lime-200 rounded-xl shadow-md transition-transform duration-300 hover:scale-102"
          in:slide={{ duration: 400 }}
        >
          <p class="text-2xl font-semibold text-gray-800">
            <span class="text-orange-500">Profile:</span> Check your awesome achievements!
          </p>
        </div>
      </TabItem>

      <!-- Settings Tab -->
      <TabItem title="⚙️ Settings">
        <div
          class="p-6 bg-orange-200 rounded-xl shadow-md transition-transform duration-300 hover:scale-102"
          in:slide={{ duration: 400 }}
        >
          <p class="text-2xl font-semibold text-gray-800">
            <span class="text-lime-500">Settings:</span> Make it your own adventure!
          </p>
        </div>
      </TabItem>

    </Tabs>

    <!-- Logout Button -->
    <button
      class="absolute top-4 right-4 bg-orange-500 text-white font-bold py-2 px-5 rounded-full shadow-md hover:bg-orange-600 transition-all duration-300 transform hover:scale-110 active:scale-95"
    >
      Logout ⏻
    </button>
  </div>
</div>

<style>
  /* Custom Animation for Slow Bounce */
  @keyframes bounceSlow {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-10px);
    }
  }

  .animate-bounce-slow {
    animation: bounceSlow 2s infinite ease-in-out;
  }

  /* Subtle Pulse for Background */
  @keyframes pulseSlow {
    0%,
    100% {
      opacity: 0.1;
    }
    50% {
      opacity: 0.15;
    }
  }

  .animate-pulse-slow {
    animation: pulseSlow 6s infinite ease-in-out;
  }

  /* Hover Scale Effect */
  .hover\:scale-102:hover {
    transform: scale(1.02);
  }
</style>