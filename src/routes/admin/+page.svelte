<!-- Correct Import -->
<script>
    import Sidebar from '$lib/components/admin/Sidebar.svelte';
</script>

<main>
    <Sidebar Title="Admin Panel" /> <!-- Note: This will cause issues because Svelte components should be PascalCase -->
</main>
