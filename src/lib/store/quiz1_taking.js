// src/lib/store/quiz1_taking.js
import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Key for localStorage
const STORAGE_KEY = 'quiz1_taking_state';

// Initial state
const initialState = {
    quizTake: 1, // Current attempt number
    maxTakes: 3, // Maximum attempts allowed
    score: 0,    // Current score
    totalPoints: 0, // Total possible points (updated when quiz data loads)
    quizCompleted: false, // Whether the quiz is completed
    showModal: false // Whether to show the result modal
};

// Load state from localStorage if in browser, otherwise use initialState
const getInitialState = () => {
    if (browser) {
        const savedState = localStorage.getItem(STORAGE_KEY);
        return savedState ? JSON.parse(savedState) : initialState;
    }
    return initialState;
};

// Create the writable store with the initial state
export const quiz1Taking = writable(getInitialState());

// Subscribe to store updates and save to localStorage only in browser
if (browser) {
    quiz1Taking.subscribe(state => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    });
}

// Function to reset the quiz state (can be called from components)
export function resetQuiz1() {
    quiz1Taking.update(state => ({
        ...state,
        quizTake: state.quizTake < state.maxTakes ? state.quizTake + 1 : state.quizTake,
        score: 0,
        quizCompleted: false,
        showModal: false
    }));
}

// Function to update score and completion status
export function submitQuiz1(newScore, totalPoints) {
    quiz1Taking.update(state => ({
        ...state,
        score: newScore,
        totalPoints: totalPoints,
        quizCompleted: true,
        showModal: newScore < totalPoints // Show modal if score isn’t perfect
    }));
}

// Function to close the modal
export function closeModal1() {
    quiz1Taking.update(state => ({
        ...state,
        showModal: false
    }));
}

// Optional: Function to fully reset the state to initial values (e.g., for a new quiz session)
export function clearQuiz1State() {
    quiz1Taking.set(initialState);
}