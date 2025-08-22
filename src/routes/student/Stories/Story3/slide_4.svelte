<script>
    import { goto } from '$app/navigation'; // Import goto for SvelteKit navigation
    import { studentData } from '$lib/store/student_data'; // Import studentData store

    let loading = false; // State to track loading status

    const slide = {
        text: "Congratulations! You've finished Part 1 of Shenievia Reads' journey through Readville Village! 🎉",
        image: "/src/assets/school-bg.gif" // Placeholder; replace with your celebratory GIF
    };

    async function continueToQuiz() {
        try {
            loading = true; // Show loading indicator

            // Get student ID from store
            let studentId;
            studentData.subscribe((data) => {
                studentId = data?.pk_studentID;
            })();

            if (!studentId) {
                console.error('No student ID found. Please log in.');
                goto('/student/quizzes/quiz2'); // Default to quiz if no student ID
                return;
            }

            // Check if the student has finalized the quiz
            const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/student-story3/check_taken_quiz3.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ student_id: studentId }),
            });

            const result = await response.json();

            if (response.ok && result.is_finalized) {
                // Quiz is finalized, navigate to trash game
                goto('/student/game/trash_3');
            } else {
                // Quiz not finalized, navigate to quiz
                goto('/student/quizzes/quiz2');
            }
        } catch (error) {
            console.error('Error checking quiz status:', error);
            // Default to quiz on error
            goto('/student/quizzes/quiz3');
        } finally {
            loading = false; // Hide loading indicator
        }
    }
</script>

<div class="flex flex-col justify-center items-center text-center slide">
    {#if slide.image}
        <div class="image-container">
            <img
                src={slide.image}
                alt="Congrats Scene"
                class="block mx-auto rounded-[2vw] shadow-lg"
            />
        </div>
    {/if}
    <p class="text-[4vw] md:text-2xl text-gray-800 font-semibold text-fade">
        {slide.text}
    </p>
    {#if loading}
        <div class="mt-[2vh] flex items-center justify-center">
            <div class="loader"></div>
            <span class="ml-2 text-[4vw] md:text-xl text-gray-800">Checking quiz status...</span>
        </div>
    {:else}
        <button
            class="mt-[2vh] bg-teal-300 text-gray-900 px-[6vw] py-[2vh] rounded-[3vw] text-[5vw] md:text-3xl font-bold shadow-md hover:bg-teal-400 hover:scale-105 transition-all duration-300 flex items-center justify-center kid-button"
            on:click={continueToQuiz}
        >
            Continue 🌟
        </button>
    {/if}
</div>

<style>
    .slide {
        animation: fadeIn 1000ms ease-in forwards;
        will-change: opacity;
    }

    .image-container {
        width: 80vw;
        height: 80vh;
        max-width: 800px;
        max-height: 400px;
        margin-bottom: 2vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .text-fade {
        white-space: pre-wrap;
        overflow-wrap: break-word;
        max-width: 80%;
        animation: textFadeIn 1000ms ease-in forwards;
        will-change: opacity;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transform: translateZ(0);
    }

    .kid-button {
        background: linear-gradient(135deg, #5eead4, #2dd4bf); /* Soft teal gradient */
        border: 3px solid #14b8a6; /* Darker teal border */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Softer shadow */
    }

    .kid-button:hover {
        background: linear-gradient(135deg, #5eead4, #2dd4bf); /* Maintain gradient on hover */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Slightly larger shadow */
    }

    .kid-button:active {
        transform: scale(1.1); /* Bounce on click */
    }

    .loader {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #14b8a6; /* Teal */
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes textFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    :global(.slide) {
        animation: fadeOut 1000ms ease-out forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>