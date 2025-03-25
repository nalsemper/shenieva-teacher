<script>
    import { Modal, Button, Toggle } from 'flowbite-svelte';
    import { Settings, Volume2, Moon, BarChart3, RefreshCcw, Lock, LogOut } from 'lucide-svelte';

    export let open = false;

    let darkMode = false;
    let soundEnabled = true;
    let animationsEnabled = true;
    let difficulty = "Normal";
    let fontSize = "Medium";

    function resetProgress() {
        if (confirm("Are you sure you want to reset all progress? This action cannot be undone.")) {
            alert("Game progress reset!");
        }
    }

    function logout() {
        alert("Logging out...");
    }
</script>

{#if open}
  <div class="fixed inset-0 flex items-center justify-center">
    <div class="backdrop-blur-lg bg-white/70 border border-gray-300 shadow-lg p-6 rounded-lg w-[400px]">
      <h3 class="text-2xl font-semibold text-gray-800 mb-4">Game Settings</h3>
      
      <!-- Preferences -->
      <div class="mb-4 text-gray-700">
          <h4 class="text-lg font-semibold mb-2">Preferences</h4>
          <div class="flex items-center justify-between mb-3">
              <span class="flex items-center space-x-2">
                  <Volume2 class="w-5 h-5 text-gray-600" />
                  <span>Enable Sound</span>
              </span>
              <Toggle bind:checked={soundEnabled} />
          </div>
          <div class="flex items-center justify-between mb-3">
              <span class="flex items-center space-x-2">
                  <BarChart3 class="w-5 h-5 text-gray-600" />
                  <span>Enable Animations</span>
              </span>
              <Toggle bind:checked={animationsEnabled} />
          </div>
          <div class="flex items-center justify-between">
              <span>Difficulty</span>
              <select bind:value={difficulty} class="px-7 py-2 border border-gray-300 bg-white text-gray-800 rounded-md">
                  <option value="Easy">Easy</option>
                  <option value="Normal">Normal</option>
                  <option value="Hard">Hard</option>
              </select>
          </div>
      </div>

      <!-- UI Customization -->
      <div class="mb-4 text-gray-700">
          <h4 class="text-lg font-semibold mb-2">UI Customization</h4>
          <div class="flex items-center justify-between mb-3">
              <span class="flex items-center space-x-2">
                  <Moon class="w-5 h-5 text-gray-600" />
                  <span>Dark Mode</span>
              </span>
              <Toggle bind:checked={darkMode} />
          </div>
          <div class="flex items-center justify-between">
              <span>Font Size</span>
              <select bind:value={fontSize} class="px-7 py-2 border border-gray-300 bg-white text-gray-800 rounded-md">
                  <option value="Small">Small</option>
                  <option value="Medium">Medium</option>
                  <option value="Large">Large</option>
              </select>
          </div>
      </div>

      <!-- Account & Privacy -->
      <div class="mb-4 text-gray-700">
          <h4 class="text-lg font-semibold mb-2">Account & Privacy</h4>
          <div class="flex items-center justify-between mb-3">
              <span class="flex items-center space-x-2">
                  <Lock class="w-5 h-5 text-gray-600" />
                  <span>Change Password</span>
              </span>
              <button class="px-3 py-1 text-blue-600">Edit</button>
          </div>
          <div class="flex items-center justify-between">
              <span class="flex items-center space-x-2">
                  <RefreshCcw class="w-5 h-5 text-red-500" />
                  <span class="text-red-500">Reset Progress</span>
              </span>
              <button on:click={resetProgress} class="px-3 py-1 text-red-500">Reset</button>
          </div>
      </div>

      <!-- Footer Buttons -->
      <div class="flex justify-between items-center">
          <button on:click={logout} class="flex items-center space-x-2 px-4 py-2 text-red-500">
              <LogOut class="w-5 h-5" />
              <span>Logout</span>
          </button>
          <Button on:click={() => (open = false)} color="alternative">Close</Button>
      </div>
    </div>
  </div>
{/if}
