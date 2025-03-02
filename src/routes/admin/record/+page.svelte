<script>

	import { Dropdown } from 'flowbite-svelte';
    import { fly } from 'svelte/transition';
    import { writable } from 'svelte/store';
  import { flip } from 'svelte/animate';
  import Attendance from './attendance.svelte';
  import Quiz1 from './quiz1.svelte';

    let activeTab = writable("attendance");
</script>


<div class=" text-gray-500 font-bold text-2xl pl-10">
    <h1>Record Management</h1>
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
                {#if tab === "attendance"} Attendance
                {:else} {tab.replace("quiz", "Quiz ")}
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
                <h2 class="text-xl font-bold text-gray-700">Quiz 2 Checking</h2>
                <p class="text-gray-500">Quiz 2 results and checking UI.</p>
            </div>
        {/if}
        {#if $activeTab === "quiz3"}
            <div in:fly={{ y: -50, opacity: 0 }} out:fly={{ y: -50, opacity: 0 }}>
                <h2 class="text-xl font-bold text-gray-700">Quiz 3 Checking</h2>
                <p class="text-gray-500">Quiz 3 results and checking UI.</p>
            </div>
        {/if}
    </div>

