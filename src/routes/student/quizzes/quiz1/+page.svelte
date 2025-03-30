<!-- src/routes/quiz/+page.svelte -->
<script>
    import { goto } from '$app/navigation';

    function navigateToGame() {
        goto('../game/trash_1');
    }
</script>

<h1>Quiz Time!</h1>
<p>Welcome to the quiz page!</p>

<button on:click={navigateToGame}>Continue</button>
