// src/lib/store/student_data.js
import { writable } from 'svelte/store';
import { browser } from '$app/environment';

/**
 * @typedef {Object} StudentData
 * @property {number} pk_studentID
 * @property {string} idNo
 * @property {string} studentName
 * @property {string} studentPass
 * @property {'Male' | 'Female'} studentGender
 * @property {number} studentLevel
 * @property {number | null} studentRibbon
 * @property {number | null} studentColtrash
 * @property {number | null} studentProgress
 */

const storedData = browser ? localStorage.getItem('studentData') : null;
const initialData = storedData !== null ? JSON.parse(storedData) : null;

/** @type {import('svelte/store').Writable<StudentData | null>} */
export const studentData = writable(initialData);

if (browser) {
  studentData.subscribe(value => {
    localStorage.setItem('studentData', JSON.stringify(value));
  });
}

// Function to reset the studentData store
export function resetStudentData() {
  studentData.set(null); // Reset to initial value (null)
  if (browser) {
    localStorage.removeItem('studentData'); // Clear from localStorage
  }
}