<script>
  import { Sidebar, SidebarGroup, SidebarItem, SidebarWrapper, SidebarDropdownItem, SidebarDropdownWrapper } from 'flowbite-svelte';
  import { ChartPieSolid, UsersSolid, FileCopyAltSolid, ClipboardCheckSolid, ArrowRightToBracketOutline } from 'flowbite-svelte-icons';
  
  // logout modal
  import { Button, Modal } from 'flowbite-svelte';
  import { ExclamationCircleOutline } from 'flowbite-svelte-icons';
  import { goto } from '$app/navigation';
  let popupModal = false;
  function logout() {
    console.log("User logged out");
    // Clear session or local storage if needed
    // localStorage.removeItem("user");

    // Navigate to login or home page
    goto('../');
  }
  
  let spanClass = 'flex-1 ms-3 whitespace-nowrap';


</script>

<Sidebar>
  <SidebarWrapper class="h-screen bg-yellow-100 flex flex-col">
    <!-- First SidebarGroup (Scrollable) -->
    <div class="flex-1 overflow-y-auto">
      <SidebarGroup>
        <SidebarItem label="Dashboard" class="hover:bg-lime-500 hover:text-white group-hover:text-white" href="/admin/dashboard">
          <svelte:fragment slot="icon">
            <ChartPieSolid class="w-6 h-6  group-hover:text-white dark:group-hover:text-white" />
          </svelte:fragment>
        </SidebarItem>

        <SidebarItem label="Students" class="hover:bg-lime-500 hover:text-white" href="/admin/students">
          <svelte:fragment slot="icon">
            <UsersSolid class="w-6 h-6" />
          </svelte:fragment>
        </SidebarItem>

        <SidebarDropdownWrapper label="Quizzes" class="hover:bg-lime-500 hover:text-white group-hover:text-white">
          <svelte:fragment slot="icon">
            <FileCopyAltSolid class="w-6 h-6  group-hover:text-white dark:group-hover:text-white" />
          </svelte:fragment>
          <SidebarDropdownItem label="Story 1" class="hover:bg-lime-500 hover:text-white" />
          <SidebarDropdownItem label="Story 2" class="hover:bg-lime-500 hover:text-white" />
          <SidebarDropdownItem label="Story 3" class="hover:bg-lime-500 hover:text-white" />
        </SidebarDropdownWrapper>

        <SidebarItem label="Checking" class="hover:bg-lime-500 hover:text-white">
          <svelte:fragment slot="icon">
            <ClipboardCheckSolid class="w-6 h-6  group-hover:text-white dark:group-hover:text-white" />
          </svelte:fragment>
        </SidebarItem>
      </SidebarGroup>
    </div>

    <!-- Second SidebarGroup (Fixed at Bottom) -->
    <SidebarGroup border class="border-t-gray-500">
      <SidebarItem label="Log Out" class="hover:bg-red-500 hover:text-white" on:click={() => (popupModal = true)}>
        <svelte:fragment slot="icon">
          <ArrowRightToBracketOutline class="w-6 h-6  dark:text-gray-400 group-hover:text-white dark:group-hover:text-white" />
        </svelte:fragment>
      </SidebarItem>
      <br><br><br>
    </SidebarGroup>
  </SidebarWrapper>
</Sidebar>
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
