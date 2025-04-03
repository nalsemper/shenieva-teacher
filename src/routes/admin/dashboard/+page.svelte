<script>
  import { Card, Button, Modal } from "flowbite-svelte";
  import {
    ExclamationCircleOutline,
    GiftBoxSolid,
    ArrowUpRightFromSquareOutline,
  } from "flowbite-svelte-icons";
  import { goto } from "$app/navigation";
  import SettingsModal from "../modals/settings.svelte";
  import modalBg from "/src/assets/icons/modal-bg.jpg";

  let showMenu = false;
  let showLogoutModal = false;
  let showSettingsModal = false; // Added for Settings Modal

  function toggleMenu() {
    showMenu = !showMenu;
  }

  function navigateToProfile() {
    goto("/admin/profile");
    showMenu = false; // Hide menu after clicking
  }

  function logout() {
    console.log("User logged out");
    goto("../");
  }
</script>

<div>
  <!-- Main Content -->
  <div class="flex-1 flex flex-col ml-64">
    <!-- Fixed Top Bar -->
    <div
      class="fixed top-0 left-64 w-[calc(100%-16rem)] bg-orange-500 text-white px-6 flex justify-between items-center shadow-md z-50"
    >
      <h1 class="text-xl font-semibold">Dashboard</h1>

      <!-- Avatar & Dropdown -->
      <div class="relative p-2">
        <button
          class="w-10 h-10 rounded-full overflow-hidden border-2 border-white"
          on:click={toggleMenu}
        >
          <img
            src="/avatar.jpg"
            alt="User Avatar"
            class="w-full h-full rounded-full border-4 border-gray-300 object-cover"
          />
        </button>

        {#if showMenu}
          <div
            class="absolute right-0 mt-2 w-48 bg-white text-black shadow-lg rounded-md py-2"
          >
            <button
              on:click={navigateToProfile}
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              >Profile</button
            >
            <button
              on:click={() => (showSettingsModal = true)}
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              >Settings</button
            >
            <button
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              on:click={() => (showLogoutModal = true)}>Logout</button
            >
          </div>
        {/if}
      </div>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4 h-screen mt-16">
    <!-- Left Column (3 Rows) -->
    <div class="grid gap-4" style="display: grid; grid-template-rows: 2fr 3fr 3fr;">
      <div class="bg-gray-200 p-4">Row 1 (Smaller)</div>
      <div class="bg-gray-300 p-4">Row 2</div>
      <div class="bg-gray-400 p-4">Row 3</div>
    </div>
  
    <!-- Right Column -->
    <div class="bg-gray-500 p-4">Right Column</div>
  </div>
  
  
</div>

<!-- Logout Confirmation Modal -->
<Modal bind:open={showLogoutModal} size="xs" autoclose>
  <div class="text-center">
    <ExclamationCircleOutline
      class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
    />
    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
      Are you sure you want to logout?
    </h3>
    <Button on:click={logout} color="red" class="me-2">Yes, I'm sure</Button>
    <Button on:click={() => (showLogoutModal = false)} color="alternative"
      >No, cancel</Button
    >
  </div>
</Modal>

<!-- Settings Modal -->
<SettingsModal bind:open={showSettingsModal} />
