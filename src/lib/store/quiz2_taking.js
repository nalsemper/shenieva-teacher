// src/lib/store/quiz2_taking.js
// @ts-nocheck
import { writable } from 'svelte/store';

export function createQuiz2Store(quizData) {
  const initialAnswers = quizData.map(q => ({ text: q.answer, assignedTo: null }));
  const store = writable({
    answers: initialAnswers,
    score: 0,
    totalPoints: 0,
    attempts: 0,
    maxAttempts: 3
  });

  return {
    subscribe: store.subscribe,
    updateAnswer(answerText, questionId) {
      store.update(state => {
        const answers = state.answers.map(ans => {
          if (ans.text === answerText) {
            return { ...ans, assignedTo: questionId };
          }
          if (ans.assignedTo === questionId && ans.text !== answerText) {
            return { ...ans, assignedTo: null };
          }
          return ans;
        });
        return { ...state, answers };
      });
    },
    submit(quizData) {
      store.update(state => {
        let score = 0;
        let totalPoints = 0;
        const answers = state.answers.map(ans => {
          const question = quizData.find(q => q.id === ans.assignedTo);
          if (question && question.answer === ans.text) {
            score += 1;
            totalPoints += question.points || 1;
          }
          return ans;
        });
        return {
          ...state,
          answers,
          score,
          totalPoints,
          attempts: state.attempts + 1
        };
      });
    },
    resetForRetake() {
      store.update(state => ({
        ...state,
        answers: state.answers.map(ans => ({ ...ans, assignedTo: null })),
        score: 0,
        totalPoints: 0
      }));
    }
  };
}