<script>
    import { name } from "$lib/store/nameStore.js"; // Import the global store
  
  </script>
  
  <main class="relative w-full h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('/src/assets/school-bg.gif');">
    <div class="absolute inset-0 bg-black/30"></div>
  
    <div class="relative text-center text-white z-10">
      <h1 class="text-4xl font-bold mb-2">Hi, {$name}!</h1>
      <p class="text-lg mb-6">Welcome to Readville. Choose an option below:</p>
  
      <nav class="flex flex-col gap-4">
        <button 
          class="bg-white/20 text-white border-2 border-white px-6 py-3 text-lg font-semibold rounded-lg transition duration-300 hover:bg-amber-500 hover:text-white"
          on:click={() => console.log("Start Game Clicked")}>
          Start Game
        </button>
        <button 
          class="bg-white/20 text-white border-2 border-white px-6 py-3 text-lg font-semibold rounded-lg transition duration-300 hover:bg-amber-500 hover:text-white"
          on:click={() => console.log("Profile Clicked")}>
          Profile
        </button>
        <button 
          class="bg-white/20 text-white border-2 border-white px-6 py-3 text-lg font-semibold rounded-lg transition duration-300 hover:bg-amber-500 hover:text-white"
          on:click={() => console.log("Settings Clicked")}>
          Settings
        </button>
      </nav>
    </div>
  </main>
  