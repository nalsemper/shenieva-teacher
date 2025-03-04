// import { writable, derived } from 'svelte/store';

// /** Store for student data.
// This assumes the data retrieved will be an array.
// **/
// export const studentData = writable([]);

// /** Derived store to extract student names. **/
// export const studentNames = derived(studentData, ($studentData) => {
//   if (Array.isArray($studentData)) {
//     return $studentData.map(student => student.studentName);
//   }
//   return [];
// });


import { writable, derived } from 'svelte/store';

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

/** @type {import('svelte/store').Writable<Student[]>} */
export const studentData = writable([]);

/** @type {import('svelte/store').Readable<string[]>} */
export const studentNames = derived(studentData, ($studentData) => {
  return $studentData.map((student) => student.studentName);
});
