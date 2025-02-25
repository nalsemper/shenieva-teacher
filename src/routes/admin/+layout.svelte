<script>
    import Sidebar from '$lib/components/admin/Sidebar.svelte';
</script>

<div class="flex h-screen">
    <!-- Sidebar (Always Visible) -->
    <Sidebar class="w-64" />

    <!-- Main Content (Dynamically Changes) -->
    <div class="flex-1 p-4">
        <slot />  <!-- Loads content from +page.svelte files -->
    </div>
</div>
