import { writable } from 'svelte/store';

// Create the store with dynamic initialization
export function createQuiz2Store(quizData) {
  // Initial state for taken quiz data
  const initialState = {
    answers: quizData.map(item => ({ text: item.answer, assignedTo: null })),
    score: 0, // Number of correct answers
    totalPoints: 0, // Sum of points for correct answers
    attempts: 0,
    maxAttempts: 3
  };

  const { subscribe, set, update } = writable(initialState);

  return {
    subscribe,
    // Reset answers, score, and totalPoints for retake, keep attempts
    resetForRetake: () =>
      update(state => ({
        ...state,
        answers: quizData.map(item => ({ text: item.answer, assignedTo: null })),
        score: 0,
        totalPoints: 0
      })),
    // Submit answers and calculate score and totalPoints
    submit: quizData =>
      update(state => {
        if (state.attempts >= state.maxAttempts) return state;
        let correctCount = 0;
        let pointsSum = 0;
        state.answers.forEach(answer => {
          const question = quizData.find(q => q.id === answer.assignedTo);
          if (question && question.answer === answer.text) {
            correctCount += 1;
            pointsSum += question.points || 1; // Use points, default to 1 if missing
          }
        });
        return {
          ...state,
          score: correctCount,
          totalPoints: pointsSum,
          attempts: state.attempts + 1
        };
      }),
    // Update answers (for drag and drop)
    updateAnswer: (answerText, assignedTo) =>
      update(state => ({
        ...state,
        answers: state.answers.map(item =>
          item.text === answerText ? { ...item, assignedTo } : item
        )
      })),
    // Reset entire quiz (e.g., for a new session)
    reset: () => set(initialState)
  };
}