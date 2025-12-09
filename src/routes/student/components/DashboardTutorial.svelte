<!-- src/routes/student/components/DashboardTutorial.svelte -->
<script>
  import { createEventDispatcher, onMount } from 'svelte';
  import { fade } from 'svelte/transition';
  import { studentData } from '$lib/store/student_data.js';

  const dispatch = createEventDispatcher();

  // Tutorial steps - Shenieva points to each button
  const tutorialSteps = [
    {
      id: 'play',
      message: 'Click here to start your adventure! Choose your level and explore exciting stories.',
      buttonSelector: '🎮',
      buttonType: 'tab'
    },
    {
      id: 'stats',
      message: 'Check your progress here! See how many quizzes you completed and track your achievements.',
      buttonSelector: '📊',
      buttonType: 'tab'
    },
    {
      id: 'home',
      message: "Visit Shenievia's home! Complete all the levels to unlock this special place and see where I live.",
      buttonSelector: '🏠',
      buttonType: 'tab'
    },
    {
      id: 'ribbons',
      message: 'Collect ribbons by completing quizzes! Check all the ribbons you earned here.',
      buttonSelector: '🎖️',
      buttonType: 'tab'
    },
    {
      id: 'logout',
      message: 'Click here when you are done! It will save your progress and take you back to the login.',
      buttonSelector: 'title="Logout"',
      buttonType: 'special'
    }
  ];

  let currentStep = 0;
  let shenieviaPosition = { x: 0, y: 0 };
  let speechPosition = { x: 0, y: 0 };
  let buttonPosition = { x: 0, y: 0 };

  $: currentStepData = tutorialSteps[currentStep];
  $: shenieviaPortrait = ($studentData?.studentGender === 'Female')
    ? '/converted/assets/Level_Walkthrough/shenievia/girl/front/1.webp'
    : '/converted/assets/Level_Walkthrough/shenievia/boy/front/1.webp';

  function getButtonElement(step) {
    if (step.buttonType === 'special') {
      return document.querySelector(`button[${step.buttonSelector}]`);
    } else {
      // Find button by emoji
      const buttons = Array.from(document.querySelectorAll('button'));
      return buttons.find(btn => btn.textContent.trim() === step.buttonSelector);
    }
  }

  function updatePositions() {
    const button = getButtonElement(currentStepData);
    if (!button) return;

    const rect = button.getBoundingClientRect();
    buttonPosition = {
      x: rect.left + rect.width / 2,
      y: rect.top + rect.height / 2
    };

    // Position Shenieva to the side of the button
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    // Decide if Shenieva should be on left or right
    const onLeft = rect.left > viewportWidth / 2;

    shenieviaPosition = {
      x: onLeft ? rect.left - 350 : rect.right + 50,
      y: Math.max(100, Math.min(rect.top - 100, viewportHeight - 550))
    };

    speechPosition = {
      x: onLeft ? rect.left - 100 : rect.right - 250,
      y: shenieviaPosition.y + 100
    };

    // Add highlight
    highlightButton(button);
  }

  function highlightButton(button) {
    // Remove previous highlights
    document.querySelectorAll('.tutorial-highlight').forEach(el => {
      el.classList.remove('tutorial-highlight');
    });
    
    if (button) {
      button.classList.add('tutorial-highlight');
    }
  }

  function nextStep() {
    if (currentStep < tutorialSteps.length - 1) {
      currentStep++;
      setTimeout(updatePositions, 100);
    } else {
      completeTutorial();
    }
  }

  function skipTutorial() {
    completeTutorial();
  }

  function completeTutorial() {
    // Cleanup highlights
    document.querySelectorAll('.tutorial-highlight').forEach(el => {
      el.classList.remove('tutorial-highlight');
    });
    
    // Don't save to localStorage - we want it to show every login
    // Just mark as shown for this session
    dispatch('complete');
  }

  onMount(() => {
    setTimeout(updatePositions, 500);

    window.addEventListener('resize', updatePositions);
    return () => {
      window.removeEventListener('resize', updatePositions);
      document.querySelectorAll('.tutorial-highlight').forEach(el => {
        el.classList.remove('tutorial-highlight');
      });
    };
  });

  $: if (currentStep >= 0) {
    setTimeout(updatePositions, 100);
  }
</script>

<!-- Overlay -->
<div 
  class="fixed inset-0 bg-black/70 z-50"
  transition:fade={{ duration: 300 }}
>
  <!-- Shenieva Character -->
  <div 
    class="absolute transition-all duration-700 ease-out pointer-events-none"
    style="left: {shenieviaPosition.x}px; top: {shenieviaPosition.y}px; width: 320px;"
  >
    <img 
      src={shenieviaPortrait} 
      alt="Shenievia" 
      class="w-full h-auto"
      style="height: 480px; width: auto; image-rendering: pixelated; filter: drop-shadow(0 10px 28px rgba(0,0,0,0.5));"
    />
  </div>

  <!-- Speech Bubble -->
  <div 
    class="absolute transition-all duration-700 ease-out pointer-events-auto"
    style="left: {speechPosition.x}px; top: {speechPosition.y}px; max-width: 380px;"
  >
    <div class="speech-bubble">
      <p class="text-gray-800 text-base leading-relaxed mb-4">
        {currentStepData.message}
      </p>

      <div class="flex justify-between items-center gap-3">
        <!-- Step indicator -->
        <div class="flex gap-1.5">
          {#each tutorialSteps as step, index}
            <div 
              class="w-2 h-2 rounded-full transition-all duration-300"
              class:bg-lime-500={index === currentStep}
              class:scale-125={index === currentStep}
              class:bg-gray-400={index !== currentStep}
            ></div>
          {/each}
        </div>

        <!-- Next button -->
        <button
          on:click={nextStep}
          class="px-5 py-2 rounded-full font-bold transition-all shadow-lg text-white"
          class:bg-lime-500={currentStep < tutorialSteps.length - 1}
          class:hover:bg-lime-600={currentStep < tutorialSteps.length - 1}
          class:bg-gradient-to-r={currentStep === tutorialSteps.length - 1}
          class:from-orange-400={currentStep === tutorialSteps.length - 1}
          class:to-lime-500={currentStep === tutorialSteps.length - 1}
        >
          {currentStep === tutorialSteps.length - 1 ? 'Got it! 🚀' : 'Next →'}
        </button>
      </div>
    </div>
  </div>

  <!-- Skip Button (top right) -->
  <button
    on:click={skipTutorial}
    class="absolute top-4 right-4 px-6 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-full font-semibold shadow-lg transition-all pointer-events-auto"
  >
    Skip Tutorial
  </button>

  <!-- Pointing Arrow/Hand -->
  <div 
    class="absolute transition-all duration-700 ease-out pointer-events-none animate-bounce-slow"
    style="left: {buttonPosition.x - 30}px; top: {buttonPosition.y - 80}px;"
  >
    <svg width="60" height="60" viewBox="0 0 60 60" class="drop-shadow-2xl">
      <path 
        d="M 30 10 L 35 25 L 50 30 L 35 35 L 30 50 L 25 35 L 10 30 L 25 25 Z" 
        fill="#FCD34D" 
        stroke="#F59E0B" 
        stroke-width="3"
      />
      <circle cx="30" cy="30" r="8" fill="#FBBF24"/>
    </svg>
  </div>
</div>

<style>
  .speech-bubble {
    position: relative;
    background: linear-gradient(180deg, rgba(248,250,252,0.98), rgba(255,255,255,0.98));
    border: 4px solid #065f46;
    padding: 20px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    font-family: 'Comic Sans MS', 'Chalkboard', cursive;
  }

  /* Highlight effect for buttons */
  :global(.tutorial-highlight) {
    animation: tutorial-pulse 1.5s ease-in-out infinite !important;
    box-shadow: 0 0 0 0 rgba(132, 204, 22, 0.9) !important;
    position: relative !important;
    z-index: 60 !important;
  }
  
  /* Scale highlight only for non-logout buttons */
  :global(.tutorial-highlight:not([title="Logout"])) {
    transform: scale(1.15) !important;
  }

  @keyframes tutorial-pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(132, 204, 22, 0.9);
    }
    50% {
      box-shadow: 0 0 0 25px rgba(132, 204, 22, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(132, 204, 22, 0);
    }
  }

  @keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
  }

  .animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
  }
</style>
