<!-- src/routes/student/components/WelcomeMessage.svelte -->
<script lang="ts">
  import { name } from "$lib/store/nameStore.js"; // Import the global store
    import { goto } from '$app/navigation';
    import { onMount } from 'svelte';
  
    // export let name: string;
    export let gender: 'boy' | 'girl';
  
    onMount(() => {
      setTimeout(() => {
        goto('/student/dashboard'); // Replace with your next route
      }, 10000);
    });
  </script>
  
  <div class="bg-gray-100 p-8 rounded-3xl shadow-lg w-full max-w-md border-4 border-lime-400 text-center">
    <h1 class="text-3xl font-bold text-lime-600 mb-6" style="font-family: 'Comic Sans MS', 'Chalkboard', cursive;">
      Welcome, {$name}! 🎉
    </h1>
    <p class="text-xl text-gray-700 mb-4" style="font-family: 'Comic Sans MS', 'Chalkboard', cursive;">
      You’re a super {gender}! Getting ready for fun...
    </p>
    <div class="text-5xl animate-bounce">✨</div>
  </div>
  
  <style>
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
  
    .animate-bounce {
      animation: bounce 1s infinite;
    }
  </style>