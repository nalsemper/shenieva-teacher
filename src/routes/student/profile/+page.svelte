<script>
    import { slide } from "svelte/transition"; // Svelte transition
  </script>
  
  <div in:slide={{ duration: 400 }}>
    <h1 class="text-4xl font-bold text-red-500 text-center mb-4 animate-bounce">
      All About You! 🌈
    </h1>
    <p class="text-xl text-gray-700 text-center">
      Look at your cool stuff!
    </p>
  </div>