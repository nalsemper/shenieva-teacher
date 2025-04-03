// import { writable, derived } from 'svelte/store';

// /**
//  * @typedef {Object} Student
//  * @property {string} pk_studentID
//  * @property {string} idNo
//  * @property {string} studentName
//  * @property {string} studentPass
//  * @property {string} studentGender
//  * @property {string} studentLevel
//  * @property {string} studentRibbon
//  * @property {string} studentColtrash
//  * @property {string} studentProgress
//  */

// /** @type {import('svelte/store').Writable<Student[]>} */
// export const studentData = writable([]);

// /** @type {import('svelte/store').Readable<string[]>} */
// export const studentNames = derived(studentData, ($studentData) => {
//   return $studentData.map((student) => student.studentName);
// });
// src/lib/store/store_students.js

// src/lib/store/store_students.js
import { writable, derived } from 'svelte/store';
import { browser } from '$app/environment';

/**
 * @typedef {Object} Student
 * @property {string} pk_studentID
 * @property {string} idNo
 * @property {string} studentName
 * @property {string} studentPass
 * @property {string} studentGender
 * @property {string} studentLevel
 * @property {string} studentRibbon
 * @property {string} studentColtrash
 * @property {string} studentProgress
 */

const storedData = browser ? localStorage.getItem('students') : null;
const initialData = storedData !== null ? JSON.parse(storedData) : [];

/** @type {import('svelte/store').Writable<Student[]>} */
export const studentData = writable(initialData);

if (browser) {
  studentData.subscribe(value => {
    localStorage.setItem('students', JSON.stringify(value));
  });
}

/** @type {import('svelte/store').Readable<string[]>} */
export const studentNames = derived(studentData, ($studentData) => {
  return $studentData.map((student) => student.studentName);
});