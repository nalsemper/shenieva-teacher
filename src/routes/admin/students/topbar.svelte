<script>
    let showMenu = false;
  
    function toggleMenu() {
      showMenu = !showMenu;
    }
  
    import { Button, Modal } from 'flowbite-svelte';
    import { ExclamationCircleOutline } from 'flowbite-svelte-icons';
    import { goto } from '$app/navigation';
  ;
  
    let popupModal = false;
  
    function logout() {
      console.log("User logged out");
      goto('../');
    }
  </script>
  
  <div>
   
    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-64">
      <!-- Fixed Top Bar -->
      <div class="fixed top-0 left-64 w-[calc(100%-16rem)] bg-orange-500 text-white px-6 py-3 flex justify-between items-center shadow-md z-50">
        <h1 class="text-xl font-semibold">Students</h1>
  
        <!-- Avatar & Dropdown -->
        <div class="relative">
          <button class="w-10 h-10 rounded-full overflow-hidden border-2 border-white" on:click={toggleMenu}>
            <img src="/avatar.png" alt="User Avatar" class="w-full h-full object-cover">
          </button>
  
          {#if showMenu}
          <div class="absolute right-0 mt-2 w-48 bg-white text-black shadow-lg rounded-md py-2">
            <a href="/profile" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
            <a href="/settings" class="block px-4 py-2 hover:bg-gray-200">Settings</a>
            <a class="block px-4 py-2 hover:bg-gray-200" on:click={() => (popupModal = true)}>Logout</a>
          </div>
          {/if}
        </div>
      </div>
    </div>
  </div>
  
  <!-- Logout Confirmation Modal -->
  <Modal bind:open={popupModal} size="xs" autoclose>
    <div class="text-center">
      <ExclamationCircleOutline class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" />
      <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
        Are you sure you want to logout?
      </h3>
      <Button on:click={logout} color="red" class="me-2">Yes, I'm sure</Button>
      <Button on:click={() => (popupModal = false)} color="alternative">No, cancel</Button>
    </div>
  </Modal>
  