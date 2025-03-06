<script>

	import { Dropdown } from 'flowbite-svelte';
    import { fly } from 'svelte/transition';
    import { writable } from 'svelte/store';
  import { flip } from 'svelte/animate';
  import Attendance from './attendance.svelte';
  import Quiz1 from './quiz1.svelte';
  import Quiz2 from './quiz2.svelte';
  import Quiz3 from './quiz3.svelte';
    let activeTab = writable("attendance");
</script>


<div class=" text-gray-500 font-bold text-2xl pl-10">
    <h1>Report Management</h1>
</div>

<div class="p-6 pb-0 max-w-6xl mx-auto">
    <!-- Tab Buttons with Improved Slide Hover Effect -->
    <div class="flex space-x-2 border-b-2 pb-2 justify-evenly">
        {#each ["attendance", "quiz1", "quiz2", "quiz3"] as tab}
            <button 
                class="group px-4 py-2 w-full font-semibold rounded-lg transition-all duration-300 ease-out transform
                {tab === $activeTab ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-200'}
                hover:-translate-y"
                on:click={() => activeTab.set(tab)}>
                {#if tab === "attendance"} Revenue Report
                {:else} {tab.replace("quiz", "Services ")}
                {/if}
            </button>
        {/each}
    </div>
</div>



    <!-- Tab Content with Smooth Sliding Transition -->
    <div class="relative overflow-hidden">
        {#if $activeTab === "attendance"}
            <div in:fly={{ y: -50, opacity: 0 }} out:fly={{ y: -50, opacity: 0 }}>
                <Attendance/>
            </div>
        {/if}
        {#if $activeTab === "quiz1"}
            <div in:fly={{ y: -50, opacity: 0 }} out:fly={{ y: -50, opacity: 0 }}>
            <Quiz1/>
            </div>
        {/if}
        {#if $activeTab === "quiz2"}
            <div in:fly={{ y: -50, opacity: 0 }} out:fly={{ y: -50, opacity: 0 }}>
                <Quiz2/>
            </div>
        {/if}
        {#if $activeTab === "quiz3"}
            <div in:fly={{ y: -50, opacity: 0 }} out:fly={{ y: -50, opacity: 0 }}>
                <Quiz3/>
            </div>
        {/if}
    </div>

