<script lang="ts">
  import { onMount } from 'svelte';

  type Student = {
    studentID: number;
    studentName: string;
    studentGender: 'Male' | 'Female' | 'Other';
    studentLevel: number;
    studentRibbon?: string;
    studentColtrash?: string;
    studentProgress?: string;
  };

  let students: Student[] = [];

  async function fetchStudents() {
    try {
      const response = await fetch('http://localhost/shenieva-teacher/api/fetch_students.php');
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const data: Student[] = await response.json();
      students = [...data]; // ✅ Reactive update
    } catch (error) {
      console.error('Error fetching students:', error);
    }
  }

  onMount(() => {
    fetchStudents();
  });
</script>

{#if students.length > 0}
  <ul>
    {#each students as student}
      <li>{student.studentName} - Level {student.studentLevel}</li>
    {/each}
  </ul>
{:else}
  <p>Loading...</p>
{/if}
