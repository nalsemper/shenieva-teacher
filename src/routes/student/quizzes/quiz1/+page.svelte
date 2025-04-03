<!-- src/routes/student/quizzes/quiz1/+page.svelte -->
<script lang="ts">
    import { goto } from '$app/navigation';
    import { onMount } from 'svelte';
    import QuizResultModal from '../../components/modals/quiz1/QuizResultModal.svelte';
    import { quiz1Taking, resetQuiz1, submitQuiz1, closeModal1 } from '$lib/store/quiz1_taking';

    interface Choice {
        id: number;
        text: string;
        is_correct: boolean;
    }

    interface Quiz {
        id: number;
        question: string;
        answer: string;
        points: number;
        choices: Choice[];
    }

    interface ApiResponse {
        success: boolean;
        data: Quiz[];
        error?: string;
    }

    let quizData: Quiz[] = [];
    let randomizedQuizData: Quiz[] = [];
    let selectedChoices: (Choice | null)[] = [];
    let loading: boolean = true;
    let error: string | null = null;

    // Subscribe to the store for reactive updates
    $: allQuestionsAnswered = selectedChoices.length > 0 && selectedChoices.every(choice => choice !== null);
    $: isPerfectScore = $quiz1Taking.score === $quiz1Taking.totalPoints;

    function shuffleArray<T>(array: T[]): T[] {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }

    onMount(async () => {
        try {
            const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/get_story1_quizzes.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const result: ApiResponse = await response.json();
            if (result.success) {
                quizData = result.data;
                randomizedQuizData = quizData.map(q => ({
                    ...q,
                    choices: shuffleArray([...q.choices])
                }));
                selectedChoices = new Array(randomizedQuizData.length).fill(null);
                // Update totalPoints in the store when quiz data loads
                quiz1Taking.update(state => ({
                    ...state,
                    totalPoints: randomizedQuizData.reduce((sum, q) => sum + q.points, 0)
                }));
            } else {
                error = result.error || 'Unknown error from API';
            }
        } catch (err: unknown) {
            error = err instanceof Error ? `Failed to load quiz data: ${err.message}` : 'Failed to load quiz data: Unknown error';
        } finally {
            loading = false;
        }
    });

    function navigateToGame(): void {
        goto('../game/trash_1');
    }

    function selectChoice(choice: Choice, questionIndex: number): void {
        selectedChoices[questionIndex] = choice;
        selectedChoices = [...selectedChoices];
        console.log('Selected choice for question', questionIndex, ':', choice);
    }

    function submitAnswers(): void {
        if (!allQuestionsAnswered) return;
        let newScore = 0;
        randomizedQuizData.forEach((quiz, index) => {
            if (selectedChoices[index]?.is_correct) {
                newScore += quiz.points;
            }
        });
        submitQuiz1(newScore, $quiz1Taking.totalPoints); // Use store function
    }

    function resetQuiz(): void {
        if ($quiz1Taking.quizTake >= $quiz1Taking.maxTakes) return;
        resetQuiz1(); // Use store function
        randomizedQuizData = quizData.map(q => ({
            ...q,
            choices: shuffleArray([...q.choices])
        }));
        selectedChoices = new Array(randomizedQuizData.length).fill(null);
    }
</script>

<div class="fixed inset-0 bg-gradient-to-br from-yellow-100 via-pink-100 to-blue-100 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="w-32 h-32 bg-yellow-300 rounded-full opacity-20 absolute -top-16 -left-16 animate-float"></div>
        <div class="w-24 h-24 bg-pink-300 rounded-full opacity-20 absolute top-20 right-10 animate-float delay-1000"></div>
        <div class="w-40 h-40 bg-blue-300 rounded-full opacity-20 absolute bottom-10 left-20 animate-float delay-2000"></div>
    </div>

    <div class="relative z-10 w-full max-w-4xl mx-auto p-6 h-screen flex flex-col">
        <div class="mb-8 text-center">
            <h1 class="text-5xl text-purple-600 font-extrabold tracking-wider drop-shadow-lg animate-bounce-slow">🎉 Quiz Adventure! 🎉</h1>
            <p class="text-xl text-gray-700 mt-2 font-semibold">
                Ready for some fun, little explorer? (Take {$quiz1Taking.quizTake} of {$quiz1Taking.maxTakes})
            </p>
        </div>

        {#if loading}
            <div class="text-center">
                <p class="text-2xl text-purple-500 animate-pulse">Loading your magical quiz... ✨</p>
                <div class="w-16 h-16 mx-auto mt-4 border-4 border-t-purple-500 border-gray-200 rounded-full animate-spin"></div>
            </div>
        {:else if error}
            <p class="text-xl text-red-400 text-center font-semibold animate-pulse">Oops! {error}</p>
        {:else if randomizedQuizData.length === 0}
            <p class="text-xl text-gray-600 text-center font-semibold">No quiz adventures yet! 🌟</p>
        {:else if !$quiz1Taking.quizCompleted}
            <div class="flex-1 overflow-y-auto space-y-6 pb-6">
                {#each randomizedQuizData as quiz, questionIndex}
                    <div class="bg-white p-6 rounded-2xl shadow-md border-4 border-purple-200 transition-all duration-500">
                        <h2 class="text-2xl text-blue-600 font-bold mb-4 flex items-center gap-3 flex-wrap">
                            <span class="w-10 h-10 flex items-center justify-center MongoDB-yellow-400 text-white rounded-full text-xl font-extrabold">{questionIndex + 1}</span>
                            {quiz.question}
                            <span class="ml-auto text-lg text-green-600 font-semibold">{quiz.points} point{quiz.points !== 1 ? 's' : ''}</span>
                        </h2>
                        <div class="grid grid-cols-1 gap-4">
                            {#each quiz.choices as choice, choiceIndex}
                                <button
                                    on:click={() => selectChoice(choice, questionIndex)}
                                    class="flex items-center gap-4 p-4 rounded-xl border-4 transition-all duration-300 hover:bg-opacity-80 w-full text-left"
                                    class:bg-yellow-100={selectedChoices[questionIndex] !== choice}
                                    class:bg-purple-500={selectedChoices[questionIndex] === choice}
                                    class:border-yellow-300={selectedChoices[questionIndex] !== choice}
                                    class:border-purple-700={selectedChoices[questionIndex] === choice}
                                    class:text-gray-800={selectedChoices[questionIndex] !== choice}
                                    class:text-white={selectedChoices[questionIndex] === choice}
                                >
                                    <span 
                                        class="w-10 h-10 flex items-center justify-center text-xl font-bold text-white rounded-full transition-all duration-300 flex-shrink-0"
                                        class:bg-purple-400={selectedChoices[questionIndex] !== choice}
                                        class:bg-purple-700={selectedChoices[questionIndex] === choice}
                                    >
                                        {String.fromCharCode(97 + choiceIndex)}
                                    </span>
                                    <span class="text-lg font-semibold flex-grow">{choice.text}</span>
                                    {#if selectedChoices[questionIndex] === choice}
                                        <span class="ml-2 text-2xl animate-bounce">⭐</span>
                                    {/if}
                                </button>
                            {/each}
                        </div>
                    </div>
                {/each}
                <div class="flex justify-end sticky bottom-0 bg-gradient-to-t from-white/80 pt-4">
                    {#if allQuestionsAnswered}
                        <button
                            on:click={submitAnswers}
                            class="py-2 px-6 bg-blue-500 text-white text-lg font-bold rounded-full transition-all duration-300 shadow-lg hover:bg-blue-600"
                        >
                            Submit 🚀
                        </button>
                    {/if}
                </div>
            </div>
        {:else}
            <div class="bg-white p-8 rounded-2xl shadow-md border-4 border-purple-200 text-center transition-all duration-500 hover:scale-105">
                <h2 class="text-4xl text-purple-600 font-extrabold mb-6 flex justify-center items-center gap-4">
                    <span class="text-5xl animate-bounce">🏆</span> You’re a Quiz Star! <span class="text-5xl animate-bounce">🏆</span>
                </h2>
                <p class="text-2xl text-gray-700 mb-6">
                    Your Score: <span class="text-green-500 font-extrabold text-3xl">{$quiz1Taking.score}</span> / {$quiz1Taking.totalPoints}
                </p>
                <div class="flex justify-center gap-6">
                    {#if $quiz1Taking.quizTake < $quiz1Taking.maxTakes && !isPerfectScore}
                        <button
                            on:click={resetQuiz}
                            class="py-3 px-8 bg-purple-500 text-white text-xl font-bold rounded-full hover:bg-purple-600 transition-all duration-300 shadow-lg hover:scale-110"
                        >
                            Try Again! 🌟
                        </button>
                    {/if}
                    <button
                        on:click={navigateToGame}
                        class="py-3 px-8 bg-blue-500 text-white text-xl font-bold rounded-full hover:bg-blue-600 transition-all duration-300 shadow-lg hover:scale-110"
                    >
                        Next Adventure! 🎮
                    </button>
                </div>
            </div>
        {/if}

        <!-- Quiz Result Modal -->
        <QuizResultModal
            showModal={$quiz1Taking.showModal}
            score={$quiz1Taking.score}
            totalPoints={$quiz1Taking.totalPoints}
            quizTake={$quiz1Taking.quizTake}
            maxTakes={$quiz1Taking.maxTakes}
            {randomizedQuizData}
            {selectedChoices}
            onClose={closeModal1}
            onRetake={resetQuiz}
        />
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }

    :global(.font-comic) {
        font-family: 'Comic Neue', 'Comic Sans MS', cursive;
    }

    .fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    button {
        width: 100%;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>